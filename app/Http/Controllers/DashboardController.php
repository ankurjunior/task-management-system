<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $today = Carbon::today()->toDateString();
        $designation = $user->designation_id
            ? DB::table('master_designations')->find($user->designation_id)
            : null;
        $canViewAllTasks = (int) $user->role_id === 1
            || (int) ($designation->hierarchy_level ?? 0) === 1
            || strcasecmp((string) ($designation->name ?? ''), 'Director') === 0;
        $dashboardMode = $canViewAllTasks ? 'overall' : 'assigned';

        $taskStatsQuery = Task::query()
            ->leftJoin('master_task_states', 'master_task_states.id', '=', 'tasks.current_state_id');

        if (! $canViewAllTasks) {
            $taskStatsQuery->where(function ($query) use ($user) {
                $query->where('tasks.created_by_user_id', $user->id)
                    ->orWhere('tasks.owner_user_id', $user->id);
            });
        }

        $openTaskFilter = function ($query) {
            $query->whereNotIn('master_task_states.state_name', ['Completed', 'Closed'])
                ->where('tasks.is_closed', false);
        };
        $visibleTasksFilter = function ($query) use ($user, $canViewAllTasks) {
            if (! $canViewAllTasks) {
                $query->where(function ($query) use ($user) {
                    $query->where('tasks.created_by_user_id', $user->id)
                        ->orWhere('tasks.owner_user_id', $user->id);
                });
            }
        };

        $dashboardStats = [
            'total' => (clone $taskStatsQuery)->count('tasks.id'),
            'in_process' => (clone $taskStatsQuery)
                ->where('master_task_states.state_name', 'WIP')
                ->count('tasks.id'),
            'completed' => (clone $taskStatsQuery)
                ->where('master_task_states.state_name', 'Completed')
                ->count('tasks.id'),
            'due_today' => (clone $taskStatsQuery)
                ->where($openTaskFilter)
                ->where(function ($query) use ($today) {
                    $query->whereDate('tasks.planned_completion_date', $today)
                        ->orWhereDate('tasks.next_update_due_date', $today);
                })
                ->count('tasks.id'),
            'overdue' => (clone $taskStatsQuery)
                ->where($openTaskFilter)
                ->where(function ($query) use ($today) {
                    $query->whereDate('tasks.planned_completion_date', '<', $today)
                        ->orWhereDate('tasks.next_update_due_date', '<', $today);
                })
                ->count('tasks.id'),
            'closed' => (clone $taskStatsQuery)
                ->where(function ($query) {
                    $query->where('master_task_states.state_name', 'Closed')
                        ->orWhere('tasks.is_closed', true);
                })
                ->count('tasks.id'),
        ];

        $cadreSummaries = DB::table('master_designations')
            ->leftJoin('users as owners', 'owners.designation_id', '=', 'master_designations.id')
            ->leftJoin('tasks', function ($join) use ($user, $canViewAllTasks) {
                $join->on('tasks.owner_user_id', '=', 'owners.id');

                if (! $canViewAllTasks) {
                    $join->where(function ($query) use ($user) {
                        $query->where('tasks.created_by_user_id', $user->id)
                            ->orWhere('tasks.owner_user_id', $user->id);
                    });
                }
            })
            ->leftJoin('master_task_states', 'master_task_states.id', '=', 'tasks.current_state_id')
            ->where('master_designations.is_active', true)
            ->where('master_designations.name', '!=', 'Director')
            ->groupBy('master_designations.id', 'master_designations.name', 'master_designations.hierarchy_level')
            ->orderBy('master_designations.hierarchy_level')
            ->orderBy('master_designations.name')
            ->select('master_designations.name as cadre')
            ->selectRaw(
                "COALESCE(SUM(CASE
                    WHEN tasks.id IS NOT NULL
                        AND master_task_states.state_name NOT IN ('Completed', 'Closed')
                        AND tasks.is_closed = 0
                        AND (tasks.planned_completion_date IS NULL OR DATE(tasks.planned_completion_date) >= ?)
                        AND (tasks.next_update_due_date IS NULL OR DATE(tasks.next_update_due_date) >= ?)
                    THEN 1 ELSE 0 END), 0) as active",
                [$today, $today]
            )
            ->selectRaw(
                "COALESCE(SUM(CASE
                    WHEN tasks.id IS NOT NULL
                        AND master_task_states.state_name = 'Completed'
                    THEN 1 ELSE 0 END), 0) as completed"
            )
            ->selectRaw(
                "COALESCE(SUM(CASE
                    WHEN tasks.id IS NOT NULL
                        AND master_task_states.state_name NOT IN ('Completed', 'Closed')
                        AND tasks.is_closed = 0
                        AND (DATE(tasks.planned_completion_date) < ? OR DATE(tasks.next_update_due_date) < ?)
                    THEN 1 ELSE 0 END), 0) as overdue",
                [$today, $today]
            )
            ->selectRaw(
                "COALESCE(SUM(CASE
                    WHEN tasks.id IS NOT NULL
                        AND (master_task_states.state_name = 'Closed' OR tasks.is_closed = 1)
                    THEN 1 ELSE 0 END), 0) as closed"
            )
            ->get();

        $statusSummaries = DB::table('master_task_states')
            ->leftJoin('tasks', function ($join) use ($visibleTasksFilter) {
                $join->on('tasks.current_state_id', '=', 'master_task_states.id');
                $visibleTasksFilter($join);
            })
            ->where('master_task_states.is_active', true)
            ->whereIn('master_task_states.state_name', ['Initial', 'WIP', 'Completed', 'Closed'])
            ->groupBy('master_task_states.id', 'master_task_states.state_name', 'master_task_states.color_code', 'master_task_states.sort_order')
            ->orderBy('master_task_states.sort_order')
            ->select([
                'master_task_states.state_name',
                'master_task_states.color_code',
            ])
            ->selectRaw('COUNT(tasks.id) as total')
            ->get()
            ->map(function ($status) use ($dashboardStats) {
                if ($status->state_name === 'Closed') {
                    $status->total = $dashboardStats['closed'] ?? 0;
                }

                $status->percentage = ($dashboardStats['total'] ?? 0) > 0
                    ? round(($status->total / $dashboardStats['total']) * 100)
                    : 0;

                return $status;
            });

        $taskListBaseQuery = Task::query()
            ->leftJoin('master_task_priorities', 'master_task_priorities.id', '=', 'tasks.priority_id')
            ->leftJoin('master_task_states', 'master_task_states.id', '=', 'tasks.current_state_id')
            ->leftJoin('users as owners', 'owners.id', '=', 'tasks.owner_user_id')
            ->select([
                'tasks.id',
                'tasks.task_name',
                'tasks.planned_completion_date',
                'tasks.next_update_due_date',
                'tasks.is_perennial',
                'owners.name as owner_name',
                'master_task_priorities.priority_name',
                'master_task_priorities.color_code as priority_color',
                'master_task_states.state_name',
            ])
            ->selectRaw('COALESCE(tasks.planned_completion_date, tasks.next_update_due_date) as due_date')
            ->where($visibleTasksFilter);

        $recentDueTasks = (clone $taskListBaseQuery)
            ->where($openTaskFilter)
            ->where(function ($query) use ($today) {
                $query->whereDate('tasks.planned_completion_date', '>=', $today)
                    ->orWhereDate('tasks.next_update_due_date', '>=', $today);
            })
            ->orderByRaw('COALESCE(tasks.planned_completion_date, tasks.next_update_due_date) asc')
            ->limit(3)
            ->get();

        $overdueTasks = (clone $taskListBaseQuery)
            ->where($openTaskFilter)
            ->where(function ($query) use ($today) {
                $query->whereDate('tasks.planned_completion_date', '<', $today)
                    ->orWhereDate('tasks.next_update_due_date', '<', $today);
            })
            ->orderByRaw('COALESCE(tasks.planned_completion_date, tasks.next_update_due_date) asc')
            ->limit(3)
            ->get()
            ->map(function ($task) use ($today) {
                $task->days_overdue = Carbon::parse($task->due_date)->diffInDays(Carbon::parse($today));

                return $task;
            });

        $dailyTaskGrowthStartDate = Carbon::today()->subDays(29)->toDateString();

        $dailyTaskCountsQuery = Task::query()
            ->selectRaw('DATE(tasks.created_at) as task_date')
            ->selectRaw('COUNT(tasks.id) as tasks_created')
            ->whereDate('tasks.created_at', '>=', $dailyTaskGrowthStartDate)
            ->whereDate('tasks.created_at', '<=', $today)
            ->where($visibleTasksFilter)
            ->groupByRaw('DATE(tasks.created_at)');

        $dailyTaskGrowth = DB::query()
            ->fromSub($dailyTaskCountsQuery, 'daily_tasks')
            ->select('task_date', 'tasks_created')
            ->selectRaw('SUM(tasks_created) OVER (ORDER BY task_date) as total_tasks_till_date')
            ->orderBy('task_date')
            ->get();

        return view('dashboard.index', compact(
            'dashboardStats',
            'cadreSummaries',
            'dashboardMode',
            'statusSummaries',
            'recentDueTasks',
            'overdueTasks',
            'dailyTaskGrowth'
        ));
    }
}
