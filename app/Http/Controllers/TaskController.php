<?php

namespace App\Http\Controllers;

use App\Jobs\SendTaskAssignedEmail;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskUpdate;
use App\Models\TaskUpdateAttachment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * TaskController
 *
 * Handles task creation, retrieval, and management operations.
 * Manages task workflow, attachments, and perennial task handling.
 *
 * @package App\Http\Controllers
 */
class TaskController extends Controller
{
    /**
     * Display a listing of tasks for the authenticated user.
     *
     * Retrieves tasks where the user is either creator or owner,
     * along with associated metadata like priority and state.
     *
     * @param Request $request The HTTP request instance
     * @return \Illuminate\View\View The tasks index view
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $canViewAllTasks = $this->canViewAllTasks($user);
        $taskFilter = $request->query('filter', 'all');
        $priorityId = (int) $request->query('priority_id') ?: null;
        $allowedFilters = [
            'all',
            'assigned_by_me',
            'assigned_to_me',
            'in_process',
            'completed',
            'due_today',
            'overdue',
            'closed',
        ];

        if (! in_array($taskFilter, $allowedFilters, true)) {
            $taskFilter = 'all';
        }

        $taskPriorities = DB::table('master_task_priorities')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($priorityId && ! $taskPriorities->contains('id', $priorityId)) {
            $priorityId = null;
        }

        $baseQuery = Task::query()
            ->leftJoin('master_task_priorities', 'master_task_priorities.id', '=', 'tasks.priority_id')
            ->leftJoin('master_task_states', 'master_task_states.id', '=', 'tasks.current_state_id')
            ->leftJoin('master_update_frequencies', 'master_update_frequencies.id', '=', 'tasks.update_frequency_id')
            ->leftJoin('users as owners', 'owners.id', '=', 'tasks.owner_user_id')
            ->leftJoin('users as creators', 'creators.id', '=', 'tasks.created_by_user_id');

        if (! $canViewAllTasks) {
            $baseQuery->where(function ($query) use ($user) {
                $query->where('tasks.created_by_user_id', $user->id)
                    ->orWhere('tasks.owner_user_id', $user->id);
            });
        }

        $today = Carbon::today()->toDateString();
        $openTaskFilter = function ($query) {
            $query->whereNotIn('master_task_states.state_name', ['Completed', 'Closed'])
                ->where('tasks.is_closed', false);
        };
        $dueTodayFilter = function ($query) use ($today) {
            $query->where(function ($query) use ($today) {
                $query->whereDate('tasks.planned_completion_date', $today)
                    ->orWhereDate('tasks.next_update_due_date', $today);
            });
        };
        $overdueFilter = function ($query) use ($today) {
            $query->where(function ($query) use ($today) {
                $query->whereDate('tasks.planned_completion_date', '<', $today)
                    ->orWhereDate('tasks.next_update_due_date', '<', $today);
            });
        };

        $filterCounts = [
            'all' => (clone $baseQuery)->count('tasks.id'),
            'assigned_by_me' => (clone $baseQuery)->where('tasks.created_by_user_id', $user->id)->count('tasks.id'),
            'assigned_to_me' => (clone $baseQuery)->where('tasks.owner_user_id', $user->id)->count('tasks.id'),
            'in_process' => (clone $baseQuery)->where('master_task_states.state_name', 'WIP')->count('tasks.id'),
            'completed' => (clone $baseQuery)->where('master_task_states.state_name', 'Completed')->count('tasks.id'),
            'due_today' => (clone $baseQuery)
                ->where($openTaskFilter)
                ->where($dueTodayFilter)
                ->count('tasks.id'),
            'overdue' => (clone $baseQuery)
                ->where($openTaskFilter)
                ->where($overdueFilter)
                ->count('tasks.id'),
            'closed' => (clone $baseQuery)
                ->where(function ($query) {
                    $query->where('master_task_states.state_name', 'Closed')
                        ->orWhere('tasks.is_closed', true);
                })
                ->count('tasks.id'),
        ];

        $applyTaskFilter = function ($query) use ($taskFilter, $user, $openTaskFilter, $dueTodayFilter, $overdueFilter) {
            match ($taskFilter) {
                'assigned_by_me' => $query->where('tasks.created_by_user_id', $user->id),
                'assigned_to_me' => $query->where('tasks.owner_user_id', $user->id),
                'in_process' => $query->where('master_task_states.state_name', 'WIP'),
                'completed' => $query->where('master_task_states.state_name', 'Completed'),
                'due_today' => $query->where($openTaskFilter)->where($dueTodayFilter),
                'overdue' => $query->where($openTaskFilter)->where($overdueFilter),
                'closed' => $query->where(function ($query) {
                    $query->where('master_task_states.state_name', 'Closed')
                        ->orWhere('tasks.is_closed', true);
                }),
                default => null,
            };
        };

        $filteredBaseQuery = clone $baseQuery;
        $applyTaskFilter($filteredBaseQuery);

        $priorityCounts = (clone $filteredBaseQuery)
            ->select('tasks.priority_id', DB::raw('count(tasks.id) as total'))
            ->groupBy('tasks.priority_id')
            ->pluck('total', 'tasks.priority_id');

        $tasksQuery = clone $filteredBaseQuery;

        if ($priorityId) {
            $tasksQuery->where('tasks.priority_id', $priorityId);
        }

        $tasks = $tasksQuery
            ->select([
                'tasks.*',
                'master_task_priorities.priority_name',
                'master_task_priorities.color_code',
                'master_task_states.state_name',
                'master_update_frequencies.frequency_name',
                'owners.name as owner_name',
                'creators.name as creator_name',
            ])
            ->latest('tasks.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('tasks.index', compact('tasks', 'taskFilter', 'filterCounts', 'taskPriorities', 'priorityId', 'priorityCounts'));
    }

    /**
     * Display the specified task details.
     *
     * Allows the task creator or assignee to view the task and update status.
     *
     * @param Request $request The HTTP request instance
     * @param Task $task The task to display
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if (! $this->canViewAllTasks($user) && $task->created_by_user_id !== $user->id && $task->owner_user_id !== $user->id) {
            abort(403);
        }

        $task->load([
            'creator',
            'owner',
            'attachments' => fn($query) => $query->with('uploader')->latest(),
            'updates' => fn($query) => $query
                ->with(['updater', 'state', 'attachments.uploader'])
                ->latest(),
        ]);

        $isOwner = $task->owner_user_id === $request->user()->id;
        $isCreator = $task->created_by_user_id === $request->user()->id;

        $allowedStates = DB::table('master_task_states')
            ->where('is_active', true)
            ->where(function ($query) use ($isOwner, $isCreator) {
                if ($isOwner) {
                    $query->orWhereIn('state_name', ['WIP', 'Completed']);
                }

                if ($isCreator) {
                    $query->orWhereIn('state_name', ['WIP', 'Closed']);
                }
            })
            ->orderBy('sort_order')
            ->get();

        $task->current_state = DB::table('master_task_states')->find($task->current_state_id);
        $task->priority = DB::table('master_task_priorities')->find($task->priority_id);
        $task->frequency = $task->update_frequency_id ? DB::table('master_update_frequencies')->find($task->update_frequency_id) : null;

        $auditLogs = DB::table('task_audit_logs')
            ->leftJoin('users', 'users.id', '=', 'task_audit_logs.user_id')
            ->where('task_audit_logs.task_id', $task->id)
            ->latest('task_audit_logs.created_at')
            ->select('task_audit_logs.*', 'users.name as user_name')
            ->limit(20)
            ->get();

        return view('tasks.show', compact('task', 'allowedStates', 'auditLogs'));
    }

    /**
     * Store a newly created task in storage.
     *
     * Validates task data, checks user hierarchy, and creates a task
     * assigned to a junior user. Handles both perennial and standard tasks.
     *
     * @param Request $request The HTTP request containing task data
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse The success/error response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->juniors()->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No junior user is assigned under your profile.',
                ], 422);
            }

            return back()
                ->withInput()
                ->with('task_error', 'No junior user is assigned under your profile.');
        }

        $data = $this->validatedTaskData($request);

        $initialState = DB::table('master_task_states')
            ->where('state_name', 'Initial')
            ->where('is_active', true)
            ->first();

        if (! $initialState) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Initial task state is not configured.',
                ], 422);
            }

            return back()
                ->withInput()
                ->with('task_error', 'Initial task state is not configured.');
        }

        $isPerennial = $request->boolean('is_perennial');
        $nextUpdateDueDate = $this->nextUpdateDueDate($data);

        $task = Task::create([
            'task_name'              => $data['task_name'],
            'task_details'           => $data['task_details'] ?? null,
            'priority_id'            => $data['priority_id'],
            'current_state_id'       => $initialState->id,
            'created_by_user_id'     => $user->id,
            'owner_user_id'          => $data['owner_user_id'],
            'planned_completion_date' => $isPerennial ? null : ($data['planned_completion_date'] ?? null),
            'is_perennial'           => $isPerennial,
            'perennial_start_date'   => $isPerennial ? ($data['perennial_start_date'] ?? null) : null,
            'perennial_end_date'     => null,
            'update_frequency_id'    => $isPerennial ? ($data['update_frequency_id'] ?? null) : null,
            'next_update_due_date'   => $isPerennial ? $nextUpdateDueDate : null,
        ]);

        $this->storeAttachments($request, $task);

        $this->recordTaskCreatedNotification($request, $task);

        SendTaskAssignedEmail::dispatch($task)->afterCommit();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task assigned to your junior.',
            ]);
        }

        return back()->with('success', 'Task assigned to your junior.');
    }

    /**
     * Update the specified task in storage.
     *
     * Only the task creator can update the task. Updates task details
     * and attachments while maintaining proper state management.
     *
     * @param Request $request The HTTP request containing updated task data
     * @param Task $task The task instance to update
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse The success/error response
     */
    public function update(Request $request, Task $task)
    {
        $user = $request->user();
        $isCreator = $task->created_by_user_id === $user->id;
        $isOwner = $task->owner_user_id === $user->id;

        if (! $isCreator && ! $isOwner) {
            abort(403);
        }

        $isClosedForNonCreator = ! $isCreator && (bool) $task->is_closed;

        
        if ($request->boolean('progress_update')) {
            if ($isClosedForNonCreator) {
                return back()->with('error', 'This task is closed. You cannot make any updates.');
            }

            $data = $request->validate([
                'remarks'                  => ['required', 'string'],
                'status_id'                => ['nullable', 'integer'],
                'completion_percentage'    => ['nullable', 'numeric', 'min:0', 'max:100'],
                'expected_completion_date' => ['nullable', 'date'],
                'update_attachments'       => ['nullable', 'array'],
                'update_attachments.*'     => ['file', 'max:10240'],
            ]);

            $state = blank($data['status_id'] ?? null)
                ? DB::table('master_task_states')->where('state_name', 'WIP')->where('is_active', true)->first()
                : DB::table('master_task_states')->where('is_active', true)->find($data['status_id']);

            if (! $state) {
                return back()->with('error', 'Selected status is not available.');
            }

            $oldStateId = $task->current_state_id;

            if ($isOwner) {
                if (! in_array($state->state_name, ['WIP', 'Completed'], true)) {
                    return back()->with('error', 'You may only mark this task as Completed or WIP.');
                }

                $task->update([
                    'current_state_id' => $state->id,
                    'actual_completion_date' => $state->state_name === 'Completed' ? now()->toDateString() : $task->actual_completion_date,
                    'is_closed' => false,
                ]);
            } elseif ($isCreator) {
                if (! in_array($state->state_name, ['WIP', 'Closed'], true)) {
                    return back()->with('error', 'You may only set the status to WIP or Closed.');
                }

                $task->update([
                    'current_state_id' => $state->id,
                    'is_closed' => $state->state_name === 'Closed',
                    'actual_completion_date' => $state->state_name === 'Closed' ? now()->toDateString() : $task->actual_completion_date,
                ]);
            }

            $this->recordTaskAuditLog(
                $request,
                $task,
                'status_update',
                'current_state_id',
                (string) $oldStateId,
                (string) $state->id,
                $data['remarks']
            );

            $taskUpdate = TaskUpdate::create([
                'task_id' => $task->id,
                'updated_by_user_id' => $user->id,
                'state_id' => $state?->id,
                'completion_percentage' => $data['completion_percentage'] ?? 0,
                'expected_completion_date' => $data['expected_completion_date'] ?? null,
                'remarks' => $data['remarks'],
            ]);

            $this->storeUpdateAttachments($request, $taskUpdate);

            $this->recordTaskAuditLog(
                $request,
                $task,
                'progress_update',
                null,
                null,
                null,
                $data['remarks']
            );

            return back()->with('success', 'Task update added successfully.');
        }

        $statusId = $request->input('status_id');
        if ($statusId) {
            $state = DB::table('master_task_states')->where('is_active', true)->find($statusId);
            if (! $state) {
                return back()->with('error', 'Selected status is not available.');
            }

            $oldStateId = $task->current_state_id;

            if ($isOwner) {
                if ($state->state_name !== 'Completed') {
                    return back()->with('error', 'You may only mark this task as Completed.');
                }

                $task->update([
                    'current_state_id' => $state->id,
                    'actual_completion_date' => now()->toDateString(),
                    'is_closed' => false,
                ]);
            } elseif ($isCreator) {
                if (! in_array($state->state_name, ['WIP', 'Closed'], true)) {
                    return back()->with('error', 'You may only set the status to WIP or Closed.');
                }

                $task->update([
                    'current_state_id' => $state->id,
                    'is_closed' => $state->state_name === 'Closed',
                    'actual_completion_date' => $state->state_name === 'Closed' ? now()->toDateString() : $task->actual_completion_date,
                ]);
            }

            TaskUpdate::create([
                'task_id' => $task->id,
                'updated_by_user_id' => $user->id,
                'state_id' => $state->id,
                'completion_percentage' => in_array($state->state_name, ['Completed', 'Closed'], true) ? 100 : 0,
                'remarks' => 'Status updated to ' . $state->state_name . '.',
            ]);

            $this->recordTaskAuditLog(
                $request,
                $task,
                'status_update',
                'current_state_id',
                (string) $oldStateId,
                (string) $state->id,
                'Status updated to ' . $state->state_name . '.'
            );

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Task status updated successfully.']);
            }

            return back()->with('success', 'Task status updated successfully.');
        }

        if ($request->boolean('attachment_upload')) {
            $request->validate([
                'attachments'   => ['required', 'array'],
                'attachments.*' => ['file', 'max:10240'],
            ]);

            $this->storeAttachments($request, $task);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Attachment uploaded successfully.',
                ]);
            }

            return back()->with('success', 'Attachment uploaded successfully.');
        }

        if (! $isCreator) {
            abort(403);
        }

        $data = $this->validatedTaskData($request);

        $isPerennial = $request->boolean('is_perennial');
        $nextUpdateDueDate = $this->nextUpdateDueDate($data);

        $task->update([
            'task_name'              => $data['task_name'],
            'task_details'           => $data['task_details'] ?? null,
            'priority_id'            => $data['priority_id'],
            'owner_user_id'          => $data['owner_user_id'],
            'planned_completion_date' => $isPerennial ? null : ($data['planned_completion_date'] ?? null),
            'is_perennial'           => $isPerennial,
            'perennial_start_date'   => $isPerennial ? ($data['perennial_start_date'] ?? null) : null,
            'perennial_end_date'     => null,
            'update_frequency_id'    => $isPerennial ? ($data['update_frequency_id'] ?? null) : null,
            'next_update_due_date'   => $isPerennial ? $nextUpdateDueDate : null,
        ]);

        $this->storeAttachments($request, $task);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Task updated successfully.',
            ]);
        }

        return back()->with('success', 'Task updated successfully.');
    }

    /**
     * Validate and return task data from the request.
     *
     * Performs validation for task creation and update operations,
     * ensuring user can only assign tasks to their direct juniors.
     *
     * @param Request $request The HTTP request instance
     * @return array<string, mixed> The validated task data
     */
    private function validatedTaskData(Request $request): array
    {
        $juniorIds = $request->user()->juniors()->pluck('id')->all();

        return $request->validate([
            'task_name'               => ['required', 'string', 'max:500'],
            'task_details'            => ['nullable', 'string'],
            'priority_id'             => ['required', 'exists:master_task_priorities,id'],
            'owner_user_id'           => ['required', Rule::in($juniorIds)],
            'planned_completion_date' => ['nullable', 'date', 'required_unless:is_perennial,1'],
            'is_perennial'            => ['nullable', 'boolean'],
            'perennial_start_date'    => ['nullable', 'date', 'required_if:is_perennial,1'],
            'update_frequency_id'     => ['nullable', 'exists:master_update_frequencies,id', 'required_if:is_perennial,1'],
            'attachments'             => ['nullable', 'array'],
            'attachments.*'           => ['file', 'max:10240'],
        ]);
    }

    /**
     * Calculate the next update due date for a perennial task.
     *
     * Computes the next update due date based on the perennial start date
     * and the configured update frequency interval.
     *
     * @param array<string, mixed> $data The task data containing perennial information
     * @return string|null The next update due date or null if not applicable
     */
    private function nextUpdateDueDate(array $data): ?string
    {
        if (blank($data['perennial_start_date'] ?? null) || blank($data['update_frequency_id'] ?? null)) {
            return null;
        }

        $frequency = DB::table('master_update_frequencies')->find($data['update_frequency_id']);

        if (! $frequency || ! $frequency->interval_days) {
            return null;
        }

        return Carbon::parse($data['perennial_start_date'])
            ->addDays($frequency->interval_days)
            ->toDateString();
    }

    private function canViewAllTasks($user): bool
    {
        if ((int) $user->role_id === 1) {
            return true;
        }

        if (! $user->designation_id) {
            return false;
        }

        $designation = DB::table('master_designations')->find($user->designation_id);

        return (int) ($designation->hierarchy_level ?? 0) === 1
            || strcasecmp((string) ($designation->name ?? ''), 'Director') === 0;
    }

    /**
     * Store task attachments from the request.
     *
     * Processes and stores uploaded files for the task, creating records
     * in the database for each attachment with metadata.
     *
     * @param Request $request The HTTP request containing files
     * @param Task $task The task to attach files to
     * @return void
     */
    private function storeAttachments(Request $request, Task $task): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('task-attachments', 'public');

            TaskAttachment::create([
                'task_id'               => $task->id,
                'uploaded_by_user_id'   => $request->user()->id,
                'original_file_name'    => $file->getClientOriginalName(),
                'stored_file_name'      => basename($path),
                'file_path'             => $path,
                'file_extension'        => $file->getClientOriginalExtension(),
                'file_size'             => $file->getSize(),
            ]);
        }
    }

    private function storeUpdateAttachments(Request $request, TaskUpdate $taskUpdate): void
    {
        if (! $request->hasFile('update_attachments')) {
            return;
        }

        foreach ($request->file('update_attachments') as $file) {
            $path = $file->store('task-update-attachments', 'public');

            TaskUpdateAttachment::create([
                'task_update_id'        => $taskUpdate->id,
                'uploaded_by_user_id'   => $request->user()->id,
                'original_file_name'    => $file->getClientOriginalName(),
                'stored_file_name'      => basename($path),
                'file_path'             => $path,
                'file_extension'        => $file->getClientOriginalExtension(),
                'file_size'             => $file->getSize(),
            ]);
        }
    }

    private function recordTaskAuditLog(
        Request $request,
        Task $task,
        string $action,
        ?string $fieldName = null,
        ?string $oldValue = null,
        ?string $newValue = null,
        ?string $remarks = null
    ): void {
        DB::table('task_audit_logs')->insert([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'remarks' => $remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function recordTaskCreatedNotification(Request $request, Task $task): void
    {
        $creator = $request->user();

        Notification::create([
            'user_id' => $task->owner_user_id,
            'task_id' => $task->id,
            'type' => 'task_created',
            'title' => 'New task assigned',
            'message' => $creator->name . ' assigned you a new task: ' . $task->task_name,
            'data' => [
                'task_id' => $task->id,
                'task_name' => $task->task_name,
                'created_by_user_id' => $task->created_by_user_id,
                'owner_user_id' => $task->owner_user_id,
                'priority_id' => $task->priority_id,
                'planned_completion_date' => $task->planned_completion_date?->toDateString(),
                'next_update_due_date' => $task->next_update_due_date?->toDateString(),
            ],
            'sent_at' => now(),
        ]);
    }
}
