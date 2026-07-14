@extends('layouts.app')

@section('title', $user->exists ? 'Edit User' : 'New User')
@section('page-title', $user->exists ? 'Edit User' : 'New User')

@section('content')
@php($editing = $user->exists)

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form method="POST" action="{{ $editing ? route('users.update', $user) : route('users.store') }}">
            @csrf
            @if ($editing)
            @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="mobile">Mobile</label>
                    <input class="form-control" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role_id">Role</label>
                    <input type="hidden" name="role_id" value="{{ $employeeRole->id }}">
                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" disabled>
                        <option value="{{ $employeeRole->id }}" selected>{{ $employeeRole->name }}</option>
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="designation_id">Designation</label>
                    <select class="form-select" id="designation_id" name="designation_id">
                        <option value="">Select designation</option>
                        @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}" data-hierarchy="{{ $designation->hierarchy_level }}" @selected(old('designation_id', $user->designation_id) == $designation->id)>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="reporting_to_user_id">Reports to</label>
                    <select class="form-select @error('reporting_to_user_id') is-invalid @enderror" id="reporting_to_user_id" name="reporting_to_user_id">
                        <option value="">Select reporting user</option>
                        @foreach ($reportingUsers as $reportingUser)
                        <option value="{{ $reportingUser->id }}" data-hierarchy="{{ $reportingUser->hierarchy_level }}" @selected(old('reporting_to_user_id', $user->reporting_to_user_id) == $reportingUser->id)>{{ $reportingUser->name }} — {{ $reportingUser->designation_name }}</option>
                        @endforeach
                    </select>
                    @error('reporting_to_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="district_id">District</label>
                    <select class="form-select" id="district_id" name="district_id">
                        <option value="">No district assignment</option>
                        @foreach ($districts as $district)
                        <option value="{{ $district->id }}" @selected(old('district_id', $user->district_id) == $district->id)>{{ $district->district_name }} ({{ $district->division_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">{{ $editing ? 'New password (optional)' : 'Password' }}</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" {{ $editing ? '' : 'required' }}>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active" required>
                        <option value="1" @selected(old('is_active', $user->is_active ?? 1) == 1)>Active</option>
                        <option value="0" @selected(old('is_active', $user->is_active) == 0)>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">{{ $editing ? 'Update User' : 'Create User' }}</button>
                <a class="btn btn-light" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const designation = document.getElementById('designation_id');
        const reportingUser = document.getElementById('reporting_to_user_id');

        const updateReportingUsers = () => {
            const selected = designation.options[designation.selectedIndex];
            const requiredLevel = Number(selected?.dataset.hierarchy || 0) - 1;

            Array.from(reportingUser.options).forEach((option) => {
                if (option.value) option.hidden = Number(option.dataset.hierarchy) !== requiredLevel;
            });

            if (reportingUser.value && reportingUser.options[reportingUser.selectedIndex].hidden) reportingUser.value = '';
            reportingUser.disabled = requiredLevel < 1;
            reportingUser.required = requiredLevel >= 1;
        };

        designation.addEventListener('change', updateReportingUsers);
        updateReportingUsers();
    });
</script>
@endpush
@endsection
