@extends('layouts.app')

@section('title', 'Task Details')
@section('page-title', 'Task Details')

@php
$formatFileSize = function ($bytes) {
if (! $bytes) {
return '-';
}

$units = ['B', 'KB', 'MB', 'GB'];
$size = (float) $bytes;
$unitIndex = 0;

while ($size >= 1024 && $unitIndex < count($units) - 1) {
    $size /=1024;
    $unitIndex++;
    }

    return number_format($size, $unitIndex===0 ? 0 : 1).' '.$units[$unitIndex];
    };

    $statusName = $task->current_state?->state_name ?? ' Not set';
    $priorityColor=$task->priority->color_code ?? '#6c757d';
    $latestUpdate = $task->updates->first();
    $isUpdateLocked = ($task->is_closed || $statusName === 'Closed') && $task->created_by_user_id !== auth()->id();
    $scheduleStatusClass = match ($statusName) {
    'Closed' => 'schedule-status-closed',
    'Completed' => 'schedule-status-completed',
    'WIP' => 'schedule-status-wip',
    'Initial' => 'schedule-status-initial',
    default => 'schedule-status-default',
    };
    @endphp

    @section('content')
    <div class="container-fluid task-show-page">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert">
            {{ session('error') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-2" role="alert">
            Please check the highlighted fields and try again.
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="task-topbar">
            <div class="task-title-block">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <span class="text-muted small fw-semibold">Task #{{ $task->id }}</span>
                    <span class="badge task-status-badge">{{ $statusName }}</span>
                    @if ($task->priority)
                    <span class="badge" style="background-color: {{ $priorityColor }}; color: #fff;">
                        {{ $task->priority->priority_name }}
                    </span>
                    @endif
                    <span class="badge {{ $task->is_perennial ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }}">
                        {{ $task->is_perennial ? 'Perennial' : 'One time' }}
                    </span>
                </div>
                <h4>{{ $task->task_name }}</h4>
                <div class="task-detail-text">{{ $task->task_details ?: 'No task details have been provided.' }}</div>
            </div>

            <div class="task-facts">
                <div class="task-fact-card task-fact-created">
                    <span>Created By - {{$task->created_at?->format('d M Y') ?? '-'}}</span>
                    <strong>{{ $task->creator?->name ?? '-' }}</strong>
                </div>

                <div class="task-fact-card task-fact-assigned">
                    <span>Assigned To</span>
                    <strong>{{ $task->owner?->name ?? '-' }}</strong>
                </div>

                <div class="task-fact-card task-fact-latest">
                    <span>Last Update</span>
                    <strong>{{ $latestUpdate?->created_at?->format('d M Y') ?? '-' }}</strong>
                </div>

                <div class="task-fact-card task-fact-due">
                    <span>{{ $task->is_perennial ? 'Next Update Date' : 'Due Date' }}</span>
                    <strong>
                        @if ($task->is_perennial)
                        {{ $task->next_update_due_date?->format('d M Y') ?? '-' }}
                        @else
                        {{ $task->planned_completion_date?->format('d M Y') ?? '-' }}
                        @endif
                    </strong>
                </div>

            </div>

            <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary task-back-btn">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back
            </a>
        </div>

        <!-- task update section Start -->
        <div class="row g-3 task-workspace">
            <div class="col-xl-12">
                <div class="panel-card task-update-card {{ $isUpdateLocked ? 'is-locked' : '' }}">
                    <div class="task-compact-header">
                        <h5>Task Update</h5>
                        <small class="text-muted"></small>
                    </div>

                    <form method="post" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="progress_update" value="1">

                        <div class="row g-3">
                            <div class="col-xl-8">
                                <textarea
                                    id="remarks"
                                    class="form-control compact-textarea @error('remarks') is-invalid @enderror"
                                    name="remarks"
                                    rows="12"
                                    placeholder="Write your task update here..."
                                    @disabled($isUpdateLocked)
                                    required>{{ old('remarks') }}</textarea>
                                @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-xl-4">
                                <div>
                                    <div class="mb-1">
                                        <label class="form-label fw-bold" for="status_id">Status change</label>
                                        <select id="status_id" name="status_id" class="form-select form-select-sm @error('status_id') is-invalid @enderror" @disabled($isUpdateLocked)>
                                            <option value="">No status change</option>
                                            @foreach ($allowedStates as $state)
                                            <option value="{{ $state->id }}" @selected(old('status_id')==$state->id)>{{ $state->state_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label  fw-bold" for="completion_percentage ">Completion</label>
                                        <div class="input-group input-group-sm">
                                            <input
                                                id="completion_percentage"
                                                class="form-control form-control-sm @error('completion_percentage') is-invalid @enderror"
                                                name="completion_percentage"
                                                type="number"
                                                min="0"
                                                max="100"
                                                step="1"
                                                value="{{ old('completion_percentage') }}"
                                                @disabled($isUpdateLocked)
                                                placeholder="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('completion_percentage')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label class="form-label fw-bold" for="expected_completion_date">Expected completion</label>
                                        <input
                                            id="expected_completion_date"
                                            class="form-control form-control-sm @error('expected_completion_date') is-invalid @enderror"
                                            name="expected_completion_date"
                                            type="date"
                                            @disabled($isUpdateLocked)
                                            value="{{ date('Y-m-d') }}">
                                        @error('expected_completion_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h6 class="fw-bold">Attachments</h6>
                                    <input class="form-control form-control-sm @error('update_attachments.*') is-invalid @enderror" name="update_attachments[]" type="file" multiple @disabled($isUpdateLocked)>
                                    @error('update_attachments.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-2">Optional attachments for this update. Maximum size: 10 MB each.</div>

                                    <button type="submit" class="btn btn-primary w-100" @disabled($isUpdateLocked)>
                                        <i class="fa-solid fa-paper-plane me-1"></i>
                                        Save update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($isUpdateLocked)
                    <div class="task-update-locked-overlay task-update-locked-overlay--hidden" role="status" aria-live="polite">
                        <div class="task-update-locked-panel">
                            <i class="fa-solid fa-lock"></i>
                            <strong>This task is closed.</strong>
                            <p class="task-update-locked-note">Updates are disabled because this task has been closed.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Task update section End -->

        <div class="row g-3 task-workspace mt-1 mb-2">
            <div class="col-xl-9">
                <div class="panel-card task-log-card mt-3 h-100">
                    <div class="task-compact-header">
                        <h5>Updates History</h5>
                        <small class="text-muted fw-bold">{{ $task->updates->count() }} Update{{ $task->updates->count() === 1 ? '' : 's' }}</small>
                    </div>

                    <div class="task-log-list">
                        @forelse ($task->updates as $update)
                        @php
                        $isCreatorUpdate = $update->updated_by_user_id === $task->created_by_user_id;
                        $isOwnerUpdate = $update->updated_by_user_id === $task->owner_user_id;
                        $updateRoleClass = $isCreatorUpdate ? 'task-log-creator' : ($isOwnerUpdate ? 'task-log-owner' : 'task-log-neutral');
                        $updateRoleLabel = $isCreatorUpdate ? 'Creator' : ($isOwnerUpdate ? 'Assigned To' : 'Team');
                        @endphp
                        <div class="task-log-item {{ $updateRoleClass }}">
                            <div class="task-log-meta">
                                <strong>
                                    <span class="task-log-role">{{ $update->updater?->name ?? '-' }}</span>
                                </strong>
                                <span>{{ $update->created_at?->format('d M Y, h:i A') ?? '-' }}</span>
                                @if ($update->state)
                                <span class="badge task-status-badge">{{ $update->state->state_name }}</span>
                                @endif
                                @if ((float) $update->completion_percentage > 0)
                                <span class="badge bg-success-subtle text-success">{{ rtrim(rtrim($update->completion_percentage, '0'), '.') }}%</span>
                                @endif
                                @if ($update->expected_completion_date)
                                <span class="text-muted small">Expected {{ $update->expected_completion_date->format('d M Y') }}</span>
                                @endif
                            </div>

                            <div class="task-log-remarks">{{ $update->remarks ?: '-' }}</div>

                            @if ($update->attachments->isNotEmpty())
                            <div class="task-log-files">
                                @foreach ($update->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" rel="noopener noreferrer">
                                    <i class="fa-solid fa-paperclip me-1"></i>
                                    {{ $attachment->original_file_name }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="task-empty-state compact">
                            <i class="fa-regular fa-message"></i>
                            <div>
                                <strong>No Recent Activity</strong>
                                <!-- <p class="mb-0 text-muted">Write the first update above.</p> -->
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>


            <div class="col-xl-3">
                <div class="panel-card task-side-card mt-3 h-100">
                    @if(0)
                    <div class="task-side-section task-schedule-card {{ $scheduleStatusClass }}" style="--schedule-priority-color: {{ $priorityColor }};">
                        <div class="task-schedule-topline">
                            <div>
                                <span>Schedule</span>
                                <strong>{{ $task->is_perennial ? 'Recurring task' : 'One-time task' }}</strong>
                            </div>
                            <div class="task-schedule-badges">
                                <span class="task-schedule-status">{{ $statusName }}</span>
                                @if ($task->priority)
                                <span class="task-schedule-priority">{{ $task->priority->priority_name }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="task-side-metric">
                            @if ($task->is_perennial)
                            <span>Frequency</span>
                            <strong>{{ $task->frequency?->frequency_name ?? '-' }}</strong>
                            @else
                            <span>Planned Completion</span>
                            <strong>{{ $task->planned_completion_date?->format('d M Y') ?? '-' }}</strong>
                            @endif
                        </div>

                        <div class="task-side-grid">
                            @if ($task->is_perennial)
                            <div>
                                <span>Start</span>
                                <strong>{{ $task->perennial_start_date?->format('d M Y') ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>Next</span>
                                <strong>{{ $task->next_update_due_date?->format('d M Y') ?? '-' }}</strong>
                            </div>
                            @else
                            @if($isUpdateLocked)
                            <div>
                                <span>Actual</span>
                                <strong>{{ $task->actual_completion_date?->format('d M Y') ?? '-' }}</strong>
                            </div>
                            @endif
                            <div>
                                <span>Status</span>
                                <strong>{{ $statusName }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="task-side-section task-attachments-section">
                        <div class="task-compact-header">
                            <h5>Attachments</h5>
                            <small class="text-muted">{{ $task->attachments->count() }} file{{ $task->attachments->count() === 1 ? '' : 's' }}</small>
                        </div>


                        @if($task->created_by_user_id === auth()->id())
                        <form class="task-side-upload" method="post" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="attachment_upload" value="1">
                            <div class="input-group input-group-sm">
                                <input class="form-control @error('attachments.*') is-invalid @enderror" name="attachments[]" type="file" multiple required>
                                <button class="btn btn-outline-primary" type="submit" title="Upload attachments" aria-label="Upload attachments">
                                    <i class="fa-solid fa-upload"></i>
                                </button>
                            </div>
                            @error('attachments.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </form>
                        @endif



                        <div class="task-attachment-list compact-list">
                            @forelse ($task->attachments as $attachment)
                            <div class="task-attachment-item">
                                <div class="task-attachment-icon">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div class="task-attachment-main">
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" rel="noopener noreferrer">
                                        {{ $attachment->original_file_name }}
                                    </a>
                                    <div class="text-muted small">
                                        {{ $formatFileSize($attachment->file_size) }}
                                        -
                                        {{ $attachment->uploader?->name ?? '-' }}
                                        -
                                        {{ $attachment->created_at?->format('d M') ?? '-' }}
                                    </div>
                                </div>
                                <a class="task-file-open" href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" rel="noopener noreferrer" title="Open attachment" aria-label="Open attachment">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                            @empty
                            <div class="task-empty-state compact">
                                <i class="fa-regular fa-folder-open"></i>
                                <div>
                                    <strong>No files</strong>
                                    <p class="mb-0 text-muted">Upload task files here.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @if(0)
                    <div class="task-side-section task-activity-section">
                        <div class="task-compact-header">
                            <h5>System Activity</h5>
                            <small class="text-muted">{{ $auditLogs->count() }} item{{ $auditLogs->count() === 1 ? '' : 's' }}</small>
                        </div>

                        <div class="task-activity-list">
                            @forelse ($auditLogs->take(8) as $auditLog)
                            <div class="task-activity-item">
                                <div>
                                    <strong>{{ str_replace('_', ' ', $auditLog->action) }}</strong>
                                    <span>{{ $auditLog->user_name ?? '-' }}</span>
                                </div>
                                <time>{{ $auditLog->created_at ? \Carbon\Carbon::parse($auditLog->created_at)->format('d M, h:i A') : '-' }}</time>
                            </div>
                            @empty
                            <div class="task-empty-state compact">
                                <i class="fa-regular fa-clock"></i>
                                <div>
                                    <strong>No activity</strong>
                                    <p class="mb-0 text-muted">System actions will appear here.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
    @endsection

    @push('styles')
    <style>
        .task-show-page {
            --task-panel-gap: 12px;
        }

        .task-topbar {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(360px, .9fr) auto;
            gap: var(--task-panel-gap);
            align-items: stretch;
            margin-bottom: var(--task-panel-gap);
            padding: 14px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(17, 24, 39, .04);
        }

        .task-title-block {
            min-width: 0;
        }

        .task-title-block h4 {
            margin: 0 0 6px;
            font-size: 19px;
            font-weight: 750;
            letter-spacing: 0;
            overflow-wrap: anywhere;
        }

        .task-detail-text {
            max-height: 42px;
            overflow: auto;
            color: #4b5563;
            font-size: 13px;
            line-height: 1.45;
            white-space: pre-line;
        }

        .task-facts {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .task-facts div {
            position: relative;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 10px 9px 12px;
            background: #f9fafb;
            min-width: 0;
            overflow: hidden;
        }

        .task-facts div::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--fact-accent, #6366f1);
        }

        .task-facts span {
            display: block;
            color: var(--fact-label, var(--muted));
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .task-facts strong {
            display: block;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .task-fact-assigned {
            --fact-accent: #2563eb;
            --fact-label: #1d4ed8;
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
        }

        .task-fact-created {
            --fact-accent: #7c3aed;
            --fact-label: #6d28d9;
            background: #f5f3ff !important;
            border-color: #ddd6fe !important;
        }

        .task-fact-due {
            --fact-accent: #0f766e;
            --fact-label: #0f766e;
            background: #f0fdfa !important;
            border-color: #99f6e4 !important;
        }

        .task-fact-latest {
            --fact-accent: #d97706;
            --fact-label: #b45309;
            background: #fffbeb !important;
            border-color: #fde68a !important;
        }

        .task-back-btn {
            align-self: start;
            white-space: nowrap;
        }

        .task-status-badge {
            background: #eef2ff;
            color: #4338ca;
        }

        .task-workspace .panel-card {
            border-radius: 8px;
            padding: 14px;
        }

        .task-compact-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .task-compact-header h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 750;
        }

        .task-update-card {
            position: relative;
            overflow: hidden;
        }

        .task-update-card.is-locked form {
            filter: blur(1.5px);
            opacity: .48;
            pointer-events: none;
            user-select: none;
        }

        .task-update-locked-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgb(249 249 249 / 72%);
            backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-14px);
            transition: opacity .35s ease, transform .35s ease;
        }

        .task-update-locked-overlay.task-update-locked-overlay--visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .task-update-main {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            min-height: 100%;
        }

        .task-update-sidebar {
            display: flex;
            flex-direction: column;
            gap: 18px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            min-height: 100%;
        }

        .task-sidebar-section {
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 12px;
            background: #fff;
            padding: 14px;
        }

        .task-sidebar-section h6 {
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .task-sidebar-footer {
            margin-top: auto;
        }

        .task-sidebar-footer .btn {
            min-height: 44px;
            font-weight: 700;
        }

        .task-sidebar-section .form-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            margin-bottom: 6px;
        }

        .task-update-main .form-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
            margin-bottom: 10px;
            color: #475569;
        }

        .task-update-main textarea {
            min-height: 220px;
            resize: vertical;
        }

        .task-sidebar-section .input-group-text {
            border-radius: 0 0.375rem 0.375rem 0;
        }

        .task-sidebar-section .form-control-sm {
            min-height: 44px;
        }

        .task-update-locked-panel {
            width: min(100%, 380px);
            border: 1px solid #d1d5db;
            border-radius: 16px;
            padding: 20px 22px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 24px 48px rgba(17, 24, 39, .16);
            text-align: center;
            transform: scale(.96);
            opacity: 0;
            transition: opacity .4s ease .1s, transform .4s ease .1s;
        }

        .task-update-locked-overlay.task-update-locked-overlay--visible .task-update-locked-panel {
            transform: scale(1);
            opacity: 1;
        }

        .task-update-locked-panel i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #eef2ff;
            color: #1d4ed8;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .task-update-locked-panel strong {
            display: block;
            margin-bottom: 10px;
            color: #0f172a;
            font-size: 18px;
        }

        .task-update-locked-note {
            margin: 0;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }

        .compact-textarea {
            resize: vertical;
            min-height: 78px;
        }

        .task-log-card {
            height: calc(100vh - 400px);
            min-height: 285px;
            display: flex;
            flex-direction: column;
        }

        .task-log-list {
            flex: 1;
            overflow: auto;
            padding-right: 3px;
        }

        .task-log-item {
            position: relative;
            border: 1px solid var(--log-border, var(--border));
            border-radius: 8px;
            padding: 10px 12px 10px 14px;
            margin-bottom: 8px;
            background: var(--log-bg, #fefefe);
            overflow: hidden;
        }

        .task-log-item::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--log-accent, #d1d5db);
        }

        .task-log-creator {
            --log-accent: #7c3aed;
            --log-border: #ddd6fe;
            --log-bg: #f5f3ff;
            --log-role-bg: #ede9fe;
            --log-role-color: #6d28d9;
        }

        .task-log-owner {
            --log-accent: #2563eb;
            --log-border: #bfdbfe;
            --log-bg: #eff6ff;
            --log-role-bg: #dbeafe;
            --log-role-color: #1d4ed8;
        }

        .task-log-neutral {
            --log-accent: #64748b;
            --log-border: #e2e8f0;
            --log-bg: #f8fafc;
            --log-role-bg: #e2e8f0;
            --log-role-color: #475569;
        }

        .task-log-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .task-log-meta strong {
            color: #111827;
        }

        .task-log-role {
            border-radius: 999px;
            padding: 2px 8px;
            background: var(--log-role-bg, #e5e7eb);
            color: var(--log-role-color, #4b5563) !important;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.4;
        }

        .task-log-meta>span:not(.badge) {
            color: var(--muted);
        }

        .task-log-remarks {
            color: #374151;
            font-size: 13px;
            line-height: 1.45;
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .task-log-files {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .task-log-files a {
            max-width: 100%;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 4px 8px;
            background: #f9fafb;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .task-log-files a:hover {
            color: var(--primary);
            border-color: #c7d2fe;
        }

        .task-side-card {
            /* height: calc(100vh - 95px); */
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow: hidden;
        }

        .task-side-section {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            padding: 10px;
            min-height: 0;
        }

        .task-schedule-card {
            position: relative;
            background: var(--schedule-bg, #f9fafb);
            border-color: var(--schedule-border, var(--border));
            flex: 0 0 auto;
            overflow: hidden;
        }

        .task-schedule-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--schedule-priority-color, var(--schedule-accent, #6366f1));
        }

        .task-schedule-topline {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
            padding-left: 2px;
        }

        .task-schedule-topline span {
            display: block;
            color: var(--schedule-label, var(--muted));
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .task-schedule-topline strong {
            display: block;
            color: #111827;
            font-size: 13px;
            line-height: 1.25;
        }

        .task-schedule-badges {
            display: flex;
            align-items: flex-end;
            flex-direction: column;
            gap: 5px;
            max-width: 48%;
        }

        .task-schedule-status,
        .task-schedule-priority {
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 11px !important;
            font-weight: 800 !important;
            line-height: 1.3;
            text-transform: none !important;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .task-schedule-status {
            background: var(--schedule-chip-bg, #eef2ff);
            color: var(--schedule-chip-color, #4338ca) !important;
        }

        .task-schedule-priority {
            background: var(--schedule-priority-color, #6c757d);
            color: #fff !important;
        }

        .schedule-status-initial {
            --schedule-bg: #eff6ff;
            --schedule-border: #bfdbfe;
            --schedule-accent: #2563eb;
            --schedule-label: #1d4ed8;
            --schedule-chip-bg: #dbeafe;
            --schedule-chip-color: #1d4ed8;
        }

        .schedule-status-wip {
            --schedule-bg: #fffbeb;
            --schedule-border: #fde68a;
            --schedule-accent: #d97706;
            --schedule-label: #b45309;
            --schedule-chip-bg: #fef3c7;
            --schedule-chip-color: #92400e;
        }

        .schedule-status-completed {
            --schedule-bg: #ecfdf5;
            --schedule-border: #a7f3d0;
            --schedule-accent: #059669;
            --schedule-label: #047857;
            --schedule-chip-bg: #d1fae5;
            --schedule-chip-color: #047857;
        }

        .schedule-status-closed {
            --schedule-bg: #f8fafc;
            --schedule-border: #cbd5e1;
            --schedule-accent: #475569;
            --schedule-label: #475569;
            --schedule-chip-bg: #e2e8f0;
            --schedule-chip-color: #334155;
        }

        .schedule-status-default {
            --schedule-bg: #f9fafb;
            --schedule-border: #e5e7eb;
            --schedule-accent: #6366f1;
            --schedule-label: #4f46e5;
            --schedule-chip-bg: #eef2ff;
            --schedule-chip-color: #4338ca;
        }

        .task-side-metric {
            margin-bottom: 8px;
            padding-left: 2px;
        }

        .task-side-metric span,
        .task-side-grid span {
            display: block;
            color: var(--schedule-label, var(--muted));
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .task-side-metric strong {
            display: block;
            color: var(--schedule-strong, #111827);
            font-size: 18px;
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .task-side-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .task-side-grid div {
            border: 1px solid var(--schedule-border, var(--border));
            border-radius: 8px;
            padding: 7px 8px;
            background: rgba(255, 255, 255, .76);
        }

        .task-side-grid strong {
            display: block;
            color: #111827;
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .task-attachments-section {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .task-activity-section {
            flex: 0 0 172px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .task-side-upload {
            margin-bottom: 10px;
        }

        .task-attachment-list {
            flex: 1;
            overflow: auto;
            min-height: 0;
        }

        .task-attachment-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid var(--border);
        }

        .task-attachment-item:last-child {
            border-bottom: 0;
        }

        .task-attachment-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            background: #ccfbf1;
            flex: 0 0 auto;
        }

        .task-attachment-main {
            min-width: 0;
            flex: 1;
        }

        .task-attachment-main a {
            display: block;
            color: #1f2937;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .task-attachment-main a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .task-file-open {
            width: 28px;
            height: 28px;
            border: 1px solid var(--border);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            flex: 0 0 auto;
            text-decoration: none;
        }

        .task-file-open:hover {
            color: var(--primary);
            border-color: #c7d2fe;
            background: #eef2ff;
        }

        .task-activity-list {
            flex: 1;
            overflow: auto;
            min-height: 0;
        }

        .task-activity-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .task-activity-item:last-child {
            border-bottom: 0;
        }

        .task-activity-item strong {
            display: block;
            color: #1f2937;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            text-transform: capitalize;
        }

        .task-activity-item span,
        .task-activity-item time {
            display: block;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.25;
        }

        .task-activity-item time {
            flex: 0 0 auto;
            text-align: right;
            max-width: 76px;
        }

        .task-empty-state {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px dashed var(--border);
            border-radius: 8px;
            padding: 14px;
            background: #f9fafb;
        }

        .task-empty-state.compact {
            margin: 8px;
        }

        .task-empty-state i {
            font-size: 20px;
            color: var(--muted);
        }

        @media (max-width: 1199.98px) {
            .task-topbar {
                grid-template-columns: 1fr;
            }

            .task-back-btn {
                position: absolute;
                top: 12px;
                right: 12px;
            }

            .task-title-block {
                padding-right: 72px;
            }

            .task-log-card,
            .task-side-card {
                height: auto;
                min-height: 0;
            }

            .task-log-list,
            .task-attachment-list,
            .task-activity-list {
                max-height: 420px;
            }

            .task-side-card {
                overflow: visible;
            }

            .task-activity-section {
                flex-basis: auto;
            }
        }

        @media (max-width: 575.98px) {
            .task-facts {
                grid-template-columns: 1fr;
            }

            .task-compact-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.querySelector('.task-update-locked-overlay');

            if (!overlay) {
                return;
            }

            window.setTimeout(function() {
                overlay.classList.add('task-update-locked-overlay--visible');
            }, 450);
        });
    </script>
    @endpush