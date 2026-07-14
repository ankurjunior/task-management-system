@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="container-fluid">

    @php
    $isOverallDashboard = ($dashboardMode ?? 'assigned') === 'overall';
    $summaryCards = [
    ['label' => 'Total Tasks', 'value' => $dashboardStats['total'] ?? 0, 'icon' => 'fa-list-check', 'tone' => 'primary', 'url' => route('tasks.index')],
    ['label' => 'WIP', 'value' => $dashboardStats['in_process'] ?? 0, 'icon' => 'fa-spinner', 'tone' => 'warning', 'url' => route('tasks.index', ['filter' => 'in_process'])],
    ['label' => 'Completed', 'value' => $dashboardStats['completed'] ?? 0, 'icon' => 'fa-circle-check', 'tone' => 'success', 'url' => route('tasks.index', ['filter' => 'completed'])],
    ['label' => 'Due Today', 'value' => $dashboardStats['due_today'] ?? 0, 'icon' => 'fa-calendar-day', 'tone' => 'info', 'url' => route('tasks.index', ['filter' => 'due_today'])],
    ['label' => 'Over Due', 'value' => $dashboardStats['overdue'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'tone' => 'danger', 'url' => route('tasks.index', ['filter' => 'overdue'])],
    ['label' => 'Closed', 'value' => $dashboardStats['closed'] ?? 0, 'icon' => 'fa-lock', 'tone' => 'secondary', 'url' => route('tasks.index', ['filter' => 'closed'])],
    ];

    $statusBadgeClass = function (?string $stateName) {
    return match ($stateName) {
    'Closed' => 'bg-dark-subtle text-dark',
    'Completed' => 'bg-success-subtle text-success',
    'WIP' => 'bg-warning-subtle text-warning',
    'Initial' => 'bg-primary-subtle text-primary',
    default => 'bg-secondary-subtle text-secondary',
    };
    };

    $statusChartData = collect($statusSummaries ?? []);
    $statusChartLabels = $statusChartData->pluck('state_name')->values()->all();
    $statusChartSeries = $statusChartData->pluck('total')->map(fn ($total) => (int) $total)->values()->all();
    $statusChartColors = $statusChartData->map(fn ($status) => $status->color_code ?: match ($status->state_name) {
    'WIP' => '#d97706',
    'Completed' => '#16a34a',
    'Closed' => '#475569',
    default => '#4f46e5',
    })->values()->all();
    $statusChartTotal = array_sum($statusChartSeries);

    $cadreChartData = collect($cadreSummaries ?? [])->map(function ($cadreSummary) {
    $cadreSummary->total = (int) $cadreSummary->active
    + (int) $cadreSummary->completed
    + (int) $cadreSummary->overdue
    + (int) $cadreSummary->closed;

    return $cadreSummary;
    });
    $cadreChartLabels = $cadreChartData->pluck('cadre')->values()->all();
    $cadreChartSeries = $cadreChartData->pluck('total')->map(fn ($total) => (int) $total)->values()->all();
    $cadreChartTotal = array_sum($cadreChartSeries);
    $cadreChartPercentages = $cadreChartData->pluck('total')->map(fn ($total) => $cadreChartTotal > 0 ? round(((int) $total / $cadreChartTotal) * 100, 1) : 0)->values()->all();
    $cadreChartColors = ['#4f46e5', '#0284c7', '#16a34a', '#d97706', '#dc2626', '#475569'];

    $dailyTaskGrowthData = collect($dailyTaskGrowth ?? []);
    $dailyTaskGrowthLabels = $dailyTaskGrowthData->pluck('task_date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('d M'))->values()->all();
    $dailyTaskCreatedSeries = $dailyTaskGrowthData->pluck('tasks_created')->map(fn ($total) => (int) $total)->values()->all();
    $dailyTaskCumulativeSeries = $dailyTaskGrowthData->pluck('total_tasks_till_date')->map(fn ($total) => (int) $total)->values()->all();
    $dailyTaskPreviousSeries = $dailyTaskGrowthData->map(fn ($taskGrowth) => max(0, (int) $taskGrowth->total_tasks_till_date - (int) $taskGrowth->tasks_created))->values()->all();
    $dailyTaskGrowthTotal = array_sum($dailyTaskCreatedSeries);
    @endphp

    <div class="row g-3 mb-4 dashboard-summary-row">
        @foreach ($summaryCards as $card)
        <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ $card['url'] }}" class="dashboard-stat-card dashboard-stat-{{ $card['tone'] }}" aria-label="Open {{ $card['label'] }} task listing">
                <div class="dashboard-stat-icon">
                    <i class="fa-solid {{ $card['icon'] }}"></i>
                </div>
                <div class="dashboard-stat-content">
                    <span>{{ $card['label'] }}</span>
                    <h2 class="dashboard-stat-value" data-target="{{ $card['value'] }}">0</h2>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-xl-6">
            <div class="panel-card h-100">
                <div class="panel-header">
                    <h5>Overdue Tasks</h5>
                    <a href="{{ route('tasks.index', ['filter' => 'overdue']) }}" class="btn btn-sm btn-light">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 ">
                        <thead>
                            <tr>
                                <th  class="text-nowrap">Delay</th>
                                <th>Task</th>
                                <th>Assigned To</th>
                                <th>Due</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($overdueTasks ?? [] as $task)
                            <tr>
                                <td class="text-danger fw-semibold text-nowrap">
                                    {{ $task->days_overdue }} {{ $task->days_overdue === 1 ? 'Day' : 'Days' }}
                                </td>
                                <td>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="dashboard-task-link">{{ $task->task_name }}</a>
                                </td>
                                <td class="text-nowrap">{{ $task->owner_name ?: '-' }}</td>
                                <td class="text-nowrap">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>

                            </tr>
                            @empty
                            <tr>
                                <td class="text-center text-muted py-4" colspan="4">No overdue tasks found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="col-xl-6">
            <div class="panel-card h-100">
                <div class="panel-header">
                    <h5>Recent Tasks</h5>
                    <a href="{{ route('tasks.index', ['filter' => 'due_today']) }}" class="btn btn-sm btn-light">View Due Today</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th class=" text-nowrap">Assigned To</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th class="text-end text-nowrap">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentDueTasks ?? [] as $task)
                            <tr>
                                <td>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="dashboard-task-link">{{ $task->task_name }}</a>
                                </td>
                                <td>{{ $task->owner_name ?: '-' }}</td>
                                <td>
                                    @if ($task->priority_name)
                                    <span class="badge text-white" style="background-color: {{ $task->priority_color ?: '#6c757d' }};">{{ $task->priority_name }}</span>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td><span class="badge {{ $statusBadgeClass($task->state_name) }}">{{ $task->state_name ?: 'Not set' }}</span></td>
                                <td class="text-end text-nowrap">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center text-muted py-4" colspan="5">No due tasks found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>



    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3">
            <div class="panel-card h-100">
                <div class="panel-header">
                    <h5>Task Status</h5>
                </div>

                @if ($statusChartTotal > 0)
                <div id="taskStatusChart" class="dashboard-status-chart"></div>
                <div class="dashboard-status-legend">
                    @foreach ($statusChartData as $index => $statusSummary)
                    <div>
                        <span style="background-color: {{ $statusChartColors[$index] ?? '#6c757d' }};"></span>
                        <strong>{{ $statusSummary->state_name }}</strong>
                        <em>{{ $statusSummary->total }}</em>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4">No status data available.</div>
                @endif

            </div>
        </div>

        <div class="col-9">
            <div class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h5>Active Tasks</h5>
                        <!-- <small class="text-muted">Cumulative task growth with daily additions highlighted</small> -->
                    </div>
                    <span class="dashboard-growth-total">{{ $dailyTaskGrowthTotal }} Total</span>
                </div>

                @if ($dailyTaskGrowthData->isNotEmpty())
                <div id="dailyTaskGrowthChart" class="dashboard-growth-chart"></div>
                @else
                <div class="text-center text-muted py-4">No task creation data available.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="row g-4">

    </div>

</div>

@endsection

@push('styles')
<style>
    .dashboard-summary-row {
        --stat-primary: #4f46e5;
        --stat-warning: #d97706;
        --stat-success: #16a34a;
        --stat-info: #0284c7;
        --stat-danger: #dc2626;
        --stat-secondary: #475569;
    }

    .dashboard-scope-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 14px;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(17, 24, 39, .04);
    }

    .dashboard-scope-bar span {
        display: block;
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .dashboard-scope-bar strong {
        display: block;
        margin-top: 3px;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.25;
    }

    .dashboard-scope-actions {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .dashboard-task-link {
        color: #111827;
        /* font-weight: 700; */
        text-decoration: none;
    }

    .dashboard-task-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .dashboard-overdue-table {
        min-width: 520px;
    }

    .dashboard-overdue-table th {
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .dashboard-overdue-table td {
        font-size: 12px;
    }

    .dashboard-status-chart {
        min-height: 145px;
        max-height: 200px;
    }

    .dashboard-cadre-chart {
        min-height: 245px;
    }

    .dashboard-growth-card .panel-header {
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 8px;
    }

    .dashboard-growth-card .panel-header small {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        line-height: 1.25;
    }

    .dashboard-growth-total {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 5px 12px;
        border: 1px solid #dbeafe;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .dashboard-growth-chart {
        min-height: 340px;
    }

    .dashboard-growth-tooltip {
        display: grid;
        gap: 5px;
        min-width: 170px;
        padding: 10px 12px;
        color: #374151;
        font-size: 12px;
        line-height: 1.25;
    }

    .dashboard-growth-tooltip strong {
        color: #111827;
        font-size: 13px;
    }

    .dashboard-growth-tooltip span,
    .dashboard-growth-tooltip em {
        display: block;
        font-style: normal;
        font-weight: 700;
    }

    .dashboard-growth-tooltip em {
        color: #2563eb;
        font-weight: 850;
    }

    .dashboard-status-legend {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 4px;
    }

    .dashboard-status-legend div {
        display: flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
        padding: 7px 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #f9fafb;
    }

    .dashboard-status-legend span {
        flex: 0 0 9px;
        width: 9px;
        height: 9px;
        border-radius: 999px;
    }

    .dashboard-status-legend strong {
        min-width: 0;
        color: #374151;
        font-size: 12px;
        font-weight: 750;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dashboard-status-legend em {
        margin-left: auto;
        color: #111827;
        font-size: 12px;
        font-style: normal;
        font-weight: 850;
    }

    .dashboard-cadre-legend {
        display: grid;
        gap: 8px;
        margin-top: 4px;
    }

    .dashboard-cadre-legend div {
        display: grid;
        grid-template-columns: 9px minmax(0, 1fr) auto;
        align-items: center;
        gap: 7px;
        padding: 7px 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: #f9fafb;
    }

    .dashboard-cadre-legend span {
        width: 9px;
        height: 9px;
        border-radius: 999px;
    }

    .dashboard-cadre-legend strong {
        min-width: 0;
        color: #374151;
        font-size: 12px;
        font-weight: 750;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dashboard-cadre-legend em {
        color: #111827;
        font-size: 12px;
        font-style: normal;
        font-weight: 850;
    }

    .dashboard-cadre-legend small {
        grid-column: 2 / -1;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.2;
    }

    .dashboard-stat-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 104px;
        padding: 16px;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding-left: 30px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(17, 24, 39, .04);
        color: inherit;
        overflow: hidden;
        text-decoration: none;
        transition: border-color .16s ease, box-shadow .16s ease, transform .16s ease;
    }

    .dashboard-stat-card:hover,
    .dashboard-stat-card:focus-visible {
        border-color: color-mix(in srgb, var(--stat-tone) 35%, var(--border));
        box-shadow: 0 10px 24px rgba(17, 24, 39, .09);
        color: inherit;
        transform: translateY(-1px);
    }

    .dashboard-stat-card:focus-visible {
        outline: 3px solid color-mix(in srgb, var(--stat-tone) 22%, transparent);
        outline-offset: 2px;
    }

    .dashboard-stat-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 8px;
        background: var(--stat-tone);
    }

    .dashboard-stat-icon {
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: color-mix(in srgb, var(--stat-tone) 12%, white);
        color: var(--stat-tone);
        font-size: 18px;
    }

    .dashboard-stat-content {
        min-width: 0;
    }

    .dashboard-stat-content span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .dashboard-stat-content h2 {
        margin: 5px 0 0;
        color: #111827;
        font-size: 28px;
        font-weight: 850;
        line-height: 1;
    }

    .dashboard-stat-primary {
        --stat-tone: var(--stat-primary);
    }

    .dashboard-stat-warning {
        --stat-tone: var(--stat-warning);
    }

    .dashboard-stat-success {
        --stat-tone: var(--stat-success);
    }

    .dashboard-stat-info {
        --stat-tone: var(--stat-info);
    }

    .dashboard-stat-danger {
        --stat-tone: var(--stat-danger);
    }

    .dashboard-stat-secondary {
        --stat-tone: var(--stat-secondary);
    }

    @media (max-width: 575.98px) {
        .dashboard-scope-bar {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-scope-actions {
            width: 100%;
        }

        .dashboard-scope-actions .btn {
            flex: 1 1 140px;
        }

        .dashboard-stat-card {
            min-height: 88px;
            padding: 14px;
        }

        .dashboard-stat-content h2 {
            font-size: 24px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.querySelector('#taskStatusChart');

        if (typeof ApexCharts === 'undefined') {
            return;
        }

        const dailyTaskGrowthElement = document.querySelector('#dailyTaskGrowthChart');
        const dailyTaskGrowthLabels = @json($dailyTaskGrowthLabels);
        const dailyTaskCreatedSeries = @json($dailyTaskCreatedSeries);
        const dailyTaskCumulativeSeries = @json($dailyTaskCumulativeSeries);
        const dailyTaskPreviousSeries = @json($dailyTaskPreviousSeries);
        const dailyTaskLabelStep = Math.max(1, Math.ceil(dailyTaskGrowthLabels.length / 12));
        const dailyTaskDisplayLabels = dailyTaskGrowthLabels.map(function(label, index) {
            return index % dailyTaskLabelStep === 0 || index === dailyTaskGrowthLabels.length - 1 ? label : '';
        });

        if (dailyTaskGrowthElement && dailyTaskCreatedSeries.some((value) => Number(value) > 0)) {
            const dailyTaskGrowthChart = new ApexCharts(dailyTaskGrowthElement, {
                chart: {
                    type: 'bar',
                    height: 240,
                    stacked: true,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                        name: 'Previous Total',
                        data: dailyTaskPreviousSeries
                    },
                    {
                        name: 'Created On Date',
                        data: dailyTaskCreatedSeries
                    }
                ],
                colors: ['#ebe597', '#008915'],
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 6,
                        borderRadiusWhenStacked: 'last',
                        columnWidth: dailyTaskGrowthLabels.length > 36 ? '42%' : '50%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#e5e7eb',
                    strokeDashArray: 3,
                    padding: {
                        left: 8,
                        right: 14,
                        bottom: 0
                    }
                },
                xaxis: {
                    categories: dailyTaskDisplayLabels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        rotate: 0,
                        hideOverlappingLabels: true,
                        trim: true,
                        style: {
                            colors: '#64748b',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: '',
                        style: {
                            color: '#374151',
                            fontSize: '11px',
                            fontWeight: 800
                        }
                    },
                    labels: {
                        formatter: function(value) {
                            return Math.round(value);
                        },
                        style: {
                            colors: '#64748b',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    fontWeight: 700,
                    offsetY: 0,
                    markers: {
                        radius: 8
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    custom: function({
                        dataPointIndex
                    }) {
                        const previousTotal = Number(dailyTaskPreviousSeries[dataPointIndex] || 0);
                        const createdToday = Number(dailyTaskCreatedSeries[dataPointIndex] || 0);
                        const totalTillDate = Number(dailyTaskCumulativeSeries[dataPointIndex] || 0);
                        const label = dailyTaskGrowthLabels[dataPointIndex] || '';

                        return '<div class="dashboard-growth-tooltip">' +
                            '<strong>' + label + '</strong>' +
                            '<span>Previous Total: ' + previousTotal + '</span>' +
                            '<span>Tasks Created: ' + createdToday + '</span>' +
                            '<em>Total Till Date: ' + totalTillDate + '</em>' +
                            '</div>';
                    }
                }
            });

            dailyTaskGrowthChart.render();
        }

        const series = @json($statusChartSeries);

        if (chartElement && series.some((value) => Number(value) > 0)) {
            const chart = new ApexCharts(chartElement, {
                chart: {
                    type: 'donut',
                    height: 200,
                    toolbar: {
                        show: false
                    }
                },
                labels: @json($statusChartLabels),
                series: series,
                colors: @json($statusChartColors),
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(value) {
                        return Math.round(value) + '%';
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 800
                    }
                },
                legend: {
                    show: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '62%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '12px',
                                    fontWeight: 800,
                                    color: '#6b7280'
                                },
                                value: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: 850,
                                    color: '#111827'
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    color: '#6b7280',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((total, value) => total + value, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' tasks';
                        }
                    }
                }
            });

            chart.render();
        }

        const cadreChartElement = document.querySelector('#cadreSummaryChart');
        const cadreSeries = @json($cadreChartPercentages);
        const cadreTotals = @json($cadreChartSeries);

        if (cadreChartElement && cadreSeries.some((value) => Number(value) > 0)) {
            const cadreChart = new ApexCharts(cadreChartElement, {
                chart: {
                    type: 'radialBar',
                    height: 250,
                    toolbar: {
                        show: false
                    }
                },
                labels: @json($cadreChartLabels),
                series: cadreSeries,
                colors: @json($cadreChartColors),
                plotOptions: {
                    radialBar: {
                        startAngle: 0,
                        endAngle: 360,
                        hollow: {
                            size: '24%',
                            background: '#fff'
                        },
                        track: {
                            background: '#e7dfd5',
                            strokeWidth: '94%',
                            margin: 7
                        },
                        dataLabels: {
                            name: {
                                show: true,
                                fontSize: '12px',
                                fontWeight: 800,
                                color: '#6b7280',
                                offsetY: -4
                            },
                            value: {
                                show: true,
                                fontSize: '20px',
                                fontWeight: 850,
                                color: '#111827',
                                offsetY: 4,
                                formatter: function(value) {
                                    return Math.round(value) + '%';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#6b7280',
                                formatter: function() {
                                    return cadreTotals.reduce((total, value) => total + Number(value), 0);
                                }
                            }
                        }
                    }
                },
                stroke: {
                    lineCap: 'round'
                },
                tooltip: {
                    y: {
                        formatter: function(value, opts) {
                            const total = cadreTotals[opts.seriesIndex] || 0;
                            return total + ' tasks (' + Math.round(value) + '%)';
                        }
                    }
                }
            });

            cadreChart.render();
        }

        if (window.jQuery) {
            $('.dashboard-stat-value').each(function() {
                const $el = $(this);
                const target = parseInt($el.data('target'), 10) || 0;

                $({
                    countNum: 0
                }).animate({
                    countNum: target
                }, {
                    duration: 1200,
                    easing: 'swing',
                    step: function() {
                        $el.text(Math.floor(this.countNum));
                    },
                    complete: function() {
                        $el.text(target);
                    }
                });
            });
        }
    });
</script>
@endpush
