@php
    $taskPriorities = DB::table('master_task_priorities')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    $updateFrequencies = DB::table('master_update_frequencies')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    $juniorUsers = auth()->user()?->juniors()
        ->with('designation')
        ->where('is_active', true)
        ->orderBy('name')
        ->get() ?? collect();
    $taskModalFields = [
        'task_name',
        'task_details',
        'priority_id',
        'owner_user_id',
        'planned_completion_date',
        'is_perennial',
        'perennial_start_date',
        'update_frequency_id',
        'attachments',
        'attachments.*',
    ];
    $taskModalHasErrors = collect($taskModalFields)->contains(fn ($field) => $errors->has($field));
@endphp

<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="js-task-form" method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if (session('task_error'))
                    <div class="alert alert-danger">
                        {{ session('task_error') }}
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <label class="form-label" for="task_name">Task Name</label>
                            <input class="form-control @error('task_name') is-invalid @enderror" id="task_name" name="task_name" value="{{ old('task_name') }}" required>
                            @error('task_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="owner_user_id">Assign To</label>
                            <select class="form-select js-live-user-select @error('owner_user_id') is-invalid @enderror" id="owner_user_id" name="owner_user_id" data-placeholder="Search user..." required>
                                <option value="">Select User</option>
                                @foreach ($juniorUsers as $juniorUser)
                                <option value="{{ $juniorUser->id }}" @selected(old('owner_user_id') == $juniorUser->id)>
                                    {{ $juniorUser->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('owner_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($juniorUsers->isEmpty())
                            <small class="text-danger">No junior user is mapped under you.</small>
                            @endif
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="task_details">Details</label>
                            <textarea class="form-control @error('task_details') is-invalid @enderror" id="task_details" name="task_details" rows="4">{{ old('task_details') }}</textarea>
                            @error('task_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="form-label" for="priority_id">Priority</label>
                            <select class="form-select @error('priority_id') is-invalid @enderror" id="priority_id" name="priority_id" required>
                                <option value="">Select priority</option>
                                @foreach ($taskPriorities as $priority)
                                <option value="{{ $priority->id }}" @selected(old('priority_id') == $priority->id)>{{ $priority->priority_name }}</option>
                                @endforeach
                            </select>
                            @error('priority_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4 col-md-6 task-planned-date-field">
                            <label class="form-label" for="planned_completion_date">Planned Completion Date</label>
                            <input class="form-control @error('planned_completion_date') is-invalid @enderror" id="planned_completion_date" name="planned_completion_date" type="date" value="{{ old('planned_completion_date') }}">
                            @error('planned_completion_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4 col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input task-perennial-toggle" id="is_perennial" name="is_perennial" type="checkbox" value="1" @checked(old('is_perennial'))>
                                <label class="form-check-label" for="is_perennial">Perennial / Ongoing Task</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="perennial_start_date">Perennial Start Date</label>
                            <input class="form-control @error('perennial_start_date') is-invalid @enderror" id="perennial_start_date" name="perennial_start_date" type="date" value="{{ old('perennial_start_date') }}">
                            @error('perennial_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="update_frequency_id">Update Frequency</label>
                            <select class="form-select @error('update_frequency_id') is-invalid @enderror" id="update_frequency_id" name="update_frequency_id">
                                <option value="">Select frequency</option>
                                @foreach ($updateFrequencies as $frequency)
                                <option value="{{ $frequency->id }}" data-interval="{{ $frequency->interval_days }}" @selected(old('update_frequency_id') == $frequency->id)>{{ $frequency->frequency_name }}</option>
                                @endforeach
                            </select>
                            @error('update_frequency_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="next_update_due_date">Next Update Due Date</label>
                            <input class="form-control" id="next_update_due_date" type="date" value="{{ old('next_update_due_date') }}" readonly>
                        </div>

                        <div class="col-lg-12">
                            <label class="form-label" for="attachments">Attachments</label>
                            <input class="form-control @error('attachments.*') is-invalid @enderror" id="attachments" name="attachments[]" type="file" multiple>
                            @error('attachments.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="progress mt-2 js-upload-progress d-none" style="height: 18px;">
                                <div
                                    class="progress-bar progress-bar-striped progress-bar-animated js-upload-progress-bar"
                                    role="progressbar"
                                    style="width: 0%;"
                                    aria-valuenow="0"
                                    aria-valuemin="0"
                                    aria-valuemax="100">0%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" @disabled($juniorUsers->isEmpty())>Save Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form class="js-task-form" id="editTaskForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <label class="form-label" for="edit_task_name">Task Name</label>
                            <input class="form-control" id="edit_task_name" name="task_name" required>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label" for="edit_owner_user_id">Assign To</label>
                            <select class="form-select js-live-user-select" id="edit_owner_user_id" name="owner_user_id" data-placeholder="Search user..." required>
                                <option value="">Select User</option>
                                @foreach ($juniorUsers as $juniorUser)
                                <option value="{{ $juniorUser->id }}">
                                    {{ $juniorUser->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="edit_task_details">Details</label>
                            <textarea class="form-control" id="edit_task_details" name="task_details" rows="4"></textarea>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <label class="form-label" for="edit_priority_id">Priority</label>
                            <select class="form-select" id="edit_priority_id" name="priority_id" required>
                                <option value="">Select priority</option>
                                @foreach ($taskPriorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->priority_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 task-planned-date-field">
                            <label class="form-label" for="edit_planned_completion_date">Planned Completion Date</label>
                            <input class="form-control" id="edit_planned_completion_date" name="planned_completion_date" type="date">
                        </div>

                        <div class="col-lg-4 col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input task-perennial-toggle" id="edit_is_perennial" name="is_perennial" type="checkbox" value="1">
                                <label class="form-check-label" for="edit_is_perennial">Perennial / Ongoing Task</label>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="edit_perennial_start_date">Perennial Start Date</label>
                            <input class="form-control" id="edit_perennial_start_date" name="perennial_start_date" type="date">
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="edit_update_frequency_id">Update Frequency</label>
                            <select class="form-select" id="edit_update_frequency_id" name="update_frequency_id">
                                <option value="">Select frequency</option>
                                @foreach ($updateFrequencies as $frequency)
                                <option value="{{ $frequency->id }}" data-interval="{{ $frequency->interval_days }}">{{ $frequency->frequency_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-4 col-md-6 task-perennial-field">
                            <label class="form-label" for="edit_next_update_due_date">Next Update Due Date</label>
                            <input class="form-control" id="edit_next_update_due_date" type="date" readonly>
                        </div>

                        <div class="col-lg-12">
                            <label class="form-label" for="edit_attachments">Add More Attachments</label>
                            <input class="form-control" id="edit_attachments" name="attachments[]" type="file" multiple>
                            <div class="progress mt-2 js-upload-progress d-none" style="height: 18px;">
                                <div
                                    class="progress-bar progress-bar-striped progress-bar-animated js-upload-progress-bar"
                                    role="progressbar"
                                    style="width: 0%;"
                                    aria-valuenow="0"
                                    aria-valuemin="0"
                                    aria-valuemax="100">0%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .select2-container--default .select2-selection--single.task-live-select {
        min-height: 38px;
        display: flex;
        align-items: center;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }

    .select2-container--default .select2-selection--single.task-live-select .select2-selection__rendered {
        padding-left: 0.75rem;
        padding-right: 2rem;
        color: #212529;
        line-height: 36px;
    }

    .select2-container--default .select2-selection--single.task-live-select .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }

    .select2-container--default .select2-selection--single.task-live-select .select2-selection__placeholder {
        color: #6c757d;
    }

    .select2-dropdown.task-live-select-dropdown {
        border-color: #dee2e6;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .14);
    }

    .select2-search--dropdown .select2-search__field {
        border-color: #dee2e6;
        border-radius: 0.375rem;
        outline: 0;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12);
    }
</style>
@endpush

@push('scripts')
<script>
    $(function() {
        const initLiveUserSelect = function() {
            if (! $.fn.select2) return;

            $('.js-live-user-select').each(function() {
                const $select = $(this);

                if ($select.data('select2')) return;

                $select.select2({
                    width: '100%',
                    placeholder: $select.data('placeholder') || 'Search user...',
                    allowClear: true,
                    dropdownParent: $select.closest('.modal'),
                    selectionCssClass: 'task-live-select',
                    dropdownCssClass: 'task-live-select-dropdown',
                });
            });
        };

        initLiveUserSelect();

        const addDays = function(dateValue, days) {
            if (! dateValue || ! days) return '';

            const date = new Date(`${dateValue}T00:00:00`);
            date.setDate(date.getDate() + Number(days));

            return date.toISOString().slice(0, 10);
        };

        const calculateNextUpdateDueDate = function($modal) {
            const $startDate = $modal.find('[name="perennial_start_date"]');
            const $frequency = $modal.find('[name="update_frequency_id"]');
            const $nextDueDate = $modal.find('[id$="next_update_due_date"]');
            const interval = $frequency.find(':selected').data('interval');

            if (! $nextDueDate.length) return;

            $nextDueDate.val(addDays($startDate.val(), interval));
        };

        const syncPerennialFields = function($modal) {
            const $toggle = $modal.find('.task-perennial-toggle');
            const $fields = $modal.find('.task-perennial-field input, .task-perennial-field select');
            const $plannedDate = $modal.find('[name="planned_completion_date"]');
            const isPerennial = $toggle.is(':checked');

            $fields.prop('disabled', ! isPerennial);

            if ($plannedDate.length) {
                $plannedDate.prop('disabled', isPerennial);

                if (isPerennial) {
                    $plannedDate.val('');
                }
            }

            calculateNextUpdateDueDate($modal);
        };

        $('.modal').each(function() {
            const $modal = $(this);
            const $toggle = $modal.find('.task-perennial-toggle');

            if (! $toggle.length) return;

            $toggle.on('change', function() {
                syncPerennialFields($modal);
            });
            $modal.find('[name="perennial_start_date"], [name="update_frequency_id"]').on('change', function() {
                calculateNextUpdateDueDate($modal);
            });
            syncPerennialFields($modal);
        });

        $('.js-task-form').on('submit', function(event) {
            event.preventDefault();

            const $form = $(this);
            const $submitButton = $form.find('[type="submit"]');
            const $progress = $form.find('.js-upload-progress');
            const $progressBar = $form.find('.js-upload-progress-bar');

            $progress.removeClass('d-none');
            $progressBar
                .removeClass('bg-danger bg-success')
                .addClass('progress-bar-animated')
                .css('width', '0%')
                .attr('aria-valuenow', 0)
                .text('0%');
            $submitButton.prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method') || 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                headers: {
                    Accept: 'application/json',
                },
                xhr: function() {
                    const xhr = $.ajaxSettings.xhr();

                    if (xhr.upload) {
                        xhr.upload.onprogress = function(event) {
                            if (! event.lengthComputable) return;

                            const percent = Math.round((event.loaded / event.total) * 100);

                            $progressBar
                                .css('width', `${percent}%`)
                                .attr('aria-valuenow', percent)
                                .text(`${percent}%`);
                        };
                    }

                    return xhr;
                },
                success: function() {
                    $progressBar
                        .removeClass('progress-bar-animated')
                        .addClass('bg-success')
                        .css('width', '100%')
                        .attr('aria-valuenow', 100)
                        .text('Completed');

                    window.location.reload();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Unable to save task.';

                    $progressBar
                        .removeClass('progress-bar-animated')
                        .addClass('bg-danger')
                        .css('width', '100%')
                        .attr('aria-valuenow', 100)
                        .text(message);
                    $submitButton.prop('disabled', false);
                },
            });
        });

        window.openEditTaskModal = function(button) {
            const $button = $(button);
            const $form = $('#editTaskForm');
            const $modal = $('#editTaskModal');

            $form.attr('action', $button.data('updateUrl'));
            $('#edit_task_name').val($button.data('taskName') || '');
            $('#edit_task_details').val($button.data('taskDetails') || '');
            $('#edit_owner_user_id').val($button.data('ownerUserId') || '').trigger('change');
            $('#edit_priority_id').val($button.data('priorityId') || '');
            $('#edit_planned_completion_date').val($button.data('plannedCompletionDate') || '');
            $('#edit_is_perennial').prop('checked', String($button.data('isPerennial')) === '1');
            $('#edit_perennial_start_date').val($button.data('perennialStartDate') || '');
            $('#edit_update_frequency_id').val($button.data('updateFrequencyId') || '');
            $('#edit_next_update_due_date').val($button.data('nextUpdateDueDate') || '');

            syncPerennialFields($modal);
            new bootstrap.Modal($modal[0]).show();
        };

        @if ($taskModalHasErrors || session('task_error'))
        new bootstrap.Modal($('#addTaskModal')[0]).show();
        @endif
    });
</script>
@endpush
