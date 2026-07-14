@extends('layouts.app')

@section('title', 'Tasks')
@section('page-title', 'Tasks')

@php
$taskFilter = $taskFilter ?? 'all';
$priorityId = $priorityId ?? null;
$taskPriorities = $taskPriorities ?? collect();
$priorityCounts = $priorityCounts ?? collect();
$filterCounts = $filterCounts ?? [
'all' => $tasks->total(),
'assigned_by_me' => 0,
'assigned_to_me' => 0,
'in_process' => 0,
'completed' => 0,
'due_today' => 0,
'overdue' => 0,
'closed' => 0,
];
$filterOptions = [
['key' => 'all', 'label' => 'All', 'icon' => 'fa-layer-group'],
['key' => 'assigned_by_me', 'label' => 'Created by me', 'icon' => 'fa-paper-plane'],
['key' => 'assigned_to_me', 'label' => 'Assigned to me', 'icon' => 'fa-user-check'],
['key' => 'in_process', 'label' => 'WIP', 'icon' => 'fa-spinner'],
['key' => 'completed', 'label' => 'Completed', 'icon' => 'fa-circle-check'],
['key' => 'due_today', 'label' => 'Due today', 'icon' => 'fa-calendar-day'],
['key' => 'overdue', 'label' => 'Over due', 'icon' => 'fa-triangle-exclamation'],
['key' => 'closed', 'label' => 'Closed', 'icon' => 'fa-lock'],
];
$activeFilterOption = collect($filterOptions)->firstWhere('key', $taskFilter) ?? $filterOptions[0];
$priorityTotal = $priorityCounts->sum();
$taskFilterUrl = function (string $filterKey) {
$params = request()->except('page', 'filter');

if ($filterKey !== 'all') {
$params['filter'] = $filterKey;
}

return route('tasks.index', $params);
};
$priorityFilterUrl = function (?int $selectedPriorityId = null) {
$params = request()->except('page', 'priority_id');

if ($selectedPriorityId) {
$params['priority_id'] = $selectedPriorityId;
}

return route('tasks.index', $params);
};
$priorityTextColor = function (?string $hexColor) {
$hex = ltrim((string) $hexColor, '#');

if (strlen($hex) !== 6) {
return '#fff';
}

$red = hexdec(substr($hex, 0, 2));
$green = hexdec(substr($hex, 2, 2));
$blue = hexdec(substr($hex, 4, 2));
$brightness = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

return $brightness > 160 ? '#111827' : '#fff';
};

$stateClass = function (?string $stateName) {
return match ($stateName) {
'Closed' => 'bg-dark-subtle text-dark',
'Completed' => 'bg-success-subtle text-success',
'WIP' => 'bg-warning-subtle text-warning',
'Initial' => 'bg-primary-subtle text-primary',
default => 'bg-secondary-subtle text-secondary',
};
};
@endphp

@section('content')
<div class="container-fluid task-index-page">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="task-list-shell">
        <div class="task-list-header">
            <div class="task-header-main">
                <div class="task-header-copy">
                    <div class="text-muted small fw-semibold">Task List</div>
                </div>

                <div class="dropdown task-filter-dropdown">
                    <button
                        class="btn task-filter-toggle dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Task filter">
                        <span class="task-filter-current">
                            <i class="fa-solid {{ $activeFilterOption['icon'] }}"></i>
                            <span>{{ $activeFilterOption['label'] }}</span>
                        </span>
                        <strong>{{ $filterCounts[$activeFilterOption['key']] ?? 0 }}</strong>
                    </button>

                    <div class="dropdown-menu task-filter-menu">
                        @foreach ($filterOptions as $filterOption)
                        <a
                            href="{{ $taskFilterUrl($filterOption['key']) }}"
                            class="dropdown-item task-filter-item {{ $taskFilter === $filterOption['key'] ? 'active' : '' }}"
                            aria-current="{{ $taskFilter === $filterOption['key'] ? 'page' : 'false' }}">
                            <span>
                                <i class="fa-solid {{ $filterOption['icon'] }}"></i>
                                <span>{{ $filterOption['label'] }}</span>
                            </span>
                            <strong>{{ $filterCounts[$filterOption['key']] ?? 0 }}</strong>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="task-priority-filter-grid" aria-label="Priority filters">

                @foreach ($taskPriorities as $priority)
                @php
                $priorityColor = $priority->color_code ?: '#6c757d';
                $priorityCount = $priorityCounts[$priority->id] ?? 0;
                @endphp
                <a
                    href="{{ $priorityFilterUrl((int) $priority->id) }}"
                    class="task-priority-filter-card {{ (int) $priorityId === (int) $priority->id ? 'is-active' : '' }}"
                    style="--priority-card-color: {{ $priorityColor }}; --priority-card-text: {{ $priorityTextColor($priorityColor) }};"
                    aria-current="{{ (int) $priorityId === (int) $priority->id ? 'page' : 'false' }}">
                    <span>
                        <i class="fa-solid fa-flag"></i>
                        {{ $priority->priority_name }}
                    </span>
                    <strong>{{ $priorityCount }}</strong>
                </a>
                @endforeach
            </div>
        </div>

        <div class="table-responsive task-table-wrap">
            <table class="table table-hover align-middle mb-0 task-table">
                <thead>
                    <tr>
                        <th class="task-col-main">Task</th>
                        <th width="140">Assigned To</th>
                        <th width="140">Schedule</th>
                        <th width="100">Status</th>
                        <th width="100px">Priority</th>
                        <th class="text-end" width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                    <tr>
                        <td class="task-col-main">
                            <div class="task-title-line">
                                <a href="{{ route('tasks.show', $task) }}">{{ $task->task_name }}</a>
                            </div>
                            <div class="task-person">
                                <span>Creator: {{ $task->creator_name ?: '-' }}</span>

                            </div>
                        </td>

                        <td>
                            <div class="task-person">
                                <strong>{{ $task->owner_name ?: '-' }}</strong>
                            </div>
                        </td>

                        <td>
                            @if ($task->is_perennial)
                            <div class="task-schedule-main">{{ $task->frequency_name ?: 'No frequency' }}</div>
                            <div class="task-schedule-sub">
                                Start {{ $task->perennial_start_date ? $task->perennial_start_date->format('d M Y') : '-' }}
                            </div>
                            <div class="task-schedule-sub">
                                Next {{ $task->next_update_due_date ? $task->next_update_due_date->format('d M Y') : '-' }}
                            </div>
                            @else
                            <div class="task-schedule-main">
                                {{ $task->planned_completion_date ? $task->planned_completion_date->format('d M Y') : '-' }}
                            </div>
                            <div class="task-schedule-sub">Planned completion</div>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column align-items-start gap-1">
                                <span class="badge {{ $stateClass($task->state_name) }}">
                                    {{ $task->state_name ?: 'Not set' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-start gap-1">
                                @if ($task->priority_name)
                                <span class="badge task-priority-badge" style="background-color: {{ $task->color_code ?: '#6c757d' }};">
                                    {{ $task->priority_name }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <td class="text-end">
                            <div class="task-actions">

                                @if ($task->created_by_user_id === auth()->id())
                                <button
                                    class="btn btn-sm btn-light"
                                    type="button"
                                    title="Edit task"
                                    aria-label="Edit task"
                                    data-update-url="{{ route('tasks.update', $task) }}"
                                    data-task-name="{{ $task->task_name }}"
                                    data-task-details="{{ $task->task_details }}"
                                    data-owner-user-id="{{ $task->owner_user_id }}"
                                    data-priority-id="{{ $task->priority_id }}"
                                    data-planned-completion-date="{{ optional($task->planned_completion_date)->format('Y-m-d') }}"
                                    data-is-perennial="{{ $task->is_perennial ? 1 : 0 }}"
                                    data-perennial-start-date="{{ optional($task->perennial_start_date)->format('Y-m-d') }}"
                                    data-update-frequency-id="{{ $task->update_frequency_id }}"
                                    data-next-update-due-date="{{ optional($task->next_update_due_date)->format('Y-m-d') }}"
                                    onclick="openEditTaskModal(this)">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                @endif
                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary" title="View task" aria-label="View task">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center text-muted py-5" colspan="7">
                            <i class="fa-regular fa-clipboard d-block mb-2 fs-4"></i>
                            No tasks found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <div class="task-list-footer">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const taskTable = $('.task-table');

        if (taskTable.length > 0 && $.fn.DataTable) {
            taskTable.DataTable({
                responsive: true,
                pageLength: 100,
                "bLengthChange": false,
                "bFilter": false,
                "bInfo": false,
                "bAutoWidth": false,
                paging: false,
                columnDefs: [{
                        orderable: false,
                        targets: 1
                    },
                    {
                        orderable: false,
                        targets: 0
                    },
                    {
                        orderable: false,
                        targets: 2
                    },
                    {
                        orderable: true,
                        targets: 3
                    },
                    {
                        orderable: true,
                        targets: 4
                    },
                    {
                        orderable: false,
                        targets: 5
                    }
                ]
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .task-list-shell {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(17, 24, 39, .04);
        overflow: hidden;
        min-height: calc(100vh - 500px);
    }

    .task-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
    }

    .task-header-main {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px 14px;
        min-width: 0;
    }

    .task-header-copy {
        min-width: 82px;
    }

    .task-filter-dropdown {
        min-width: 210px;
    }

    .task-filter-toggle {
        width: 100%;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 6px 10px;
        border: 1px solid #d7dee8;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    }

    .task-filter-toggle:hover,
    .task-filter-toggle:focus {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: var(--primary);
    }

    .task-filter-current,
    .task-filter-item span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        font-size: 13px;
        font-weight: 750;
        line-height: 1;
    }

    .task-filter-current i,
    .task-filter-item i {
        width: 15px;
        color: #64748b;
        font-size: 12px;
        text-align: center;
    }

    .task-filter-toggle strong,
    .task-filter-item strong {
        min-width: 24px;
        padding: 4px 7px;
        border-radius: 999px;
        background: #e5e7eb;
        color: #374151;
        font-size: 11px;
        font-weight: 850;
        line-height: 1;
        text-align: center;
    }

    .task-filter-menu {
        width: 260px;
        padding: 6px;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .12);
    }

    .task-filter-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 10px;
        border-radius: 6px;
        color: #374151;
    }

    .task-filter-item:hover,
    .task-filter-item:focus {
        background: #f8fafc;
        color: var(--primary);
    }

    .task-filter-item.active {
        background: var(--primary);
        color: #fff;
    }

    .task-filter-item.active i {
        color: #fff;
    }

    .task-filter-item.active strong {
        background: rgba(255, 255, 255, .22);
        color: #fff;
    }

    .task-priority-filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(104px, 1fr));
        gap: 8px;
        width: min(100%, 460);
    }

    .task-priority-filter-card {
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px;
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 8px;
        background: var(--priority-card-color, #64748b);
        color: var(--priority-card-text, #fff);
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
        transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
    }

    .task-priority-filter-card:hover,
    .task-priority-filter-card:focus {
        color: var(--priority-card-text, #fff);
        filter: saturate(1.06);
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, .14);
    }

    .task-priority-filter-card.is-active {
        outline: 2px solid rgba(15, 23, 42, .16);
        outline-offset: 2px;
    }

    .task-priority-filter-card span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.15;
    }

    .task-priority-filter-card span i {
        width: 14px;
        font-size: 11px;
        text-align: center;
        opacity: .9;
    }

    .task-priority-filter-card strong {
        min-width: 28px;
        padding: 4px 7px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .24);
        color: inherit;
        font-size: 12px;
        font-weight: 900;
        line-height: 1;
        text-align: center;
    }

    .task-priority-all {
        --priority-card-color: #f8fafc;
        --priority-card-text: #1f2937;
        border-color: #d7dee8;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
    }

    .task-priority-all strong {
        background: #e5e7eb;
    }

    .task-table-wrap {
        min-height: calc(100vh - 325px);
    }

    .task-table {
        min-width: 980px;
    }

    .task-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f9fafb;
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
        padding: 8px 8px;
    }

    .task-table tbody td {
        padding: 7px 7px;
        border-color: var(--border);
        vertical-align: middle;
    }

    .task-col-main {
        width: 45%;
        min-width: 520px;
    }

    .task-title-line a {
        display: inline-block;
        max-width: 100%;
        color: #111827;
        font-weight: 750;
        line-height: 1.25;
        text-decoration: none;
        overflow-wrap: anywhere;
    }

    .task-title-line a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .task-description-line {
        display: -webkit-box;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.35;
        overflow: hidden;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .task-mobile-meta {
        display: none;
    }

    .task-person span,
    .task-schedule-sub {
        display: block;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.25;
    }

    .task-person strong,
    .task-schedule-main {
        display: block;
        color: #1f2937;
        font-size: 13px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .task-priority-badge {
        color: #fff;
    }

    .task-actions {
        display: inline-flex;
        justify-content: flex-end;
        gap: 6px;
        white-space: nowrap;
    }

    .task-actions .btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .task-list-footer {
        padding: 10px 14px;
        border-top: 1px solid var(--border);
        background: #fff;
    }

    .task-list-footer nav {
        margin: 0;
    }

    .badge {
        font-size: 13px !important;
        font-weight: bold;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    @media (max-width: 991.98px) {
        .task-list-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .task-header-main,
        .task-priority-filter-grid {
            justify-content: flex-start;
            width: 100%;
        }

        .task-filter-dropdown {
            width: 100%;
        }

        .task-priority-filter-grid {
            grid-template-columns: repeat(auto-fit, minmax(118px, 1fr));
        }

        .task-table-wrap {
            max-height: none;
        }
    }

    @media (max-width: 767.98px) {
        .task-table {
            min-width: 760px;
        }

        .task-col-main {
            min-width: 260px;
        }

        .task-mobile-meta {
            display: flex;
            gap: 8px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 11px;
        }
    }
</style>
@endpush
