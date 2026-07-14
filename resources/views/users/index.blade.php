@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@push('styles')
<style>
    .users-filter-card {
        position: sticky;
        top: 75px;
        z-index: 900;
    }

    @media (max-width: 767.98px) {
        .users-filter-card {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-0">User Listing</h5>
            <small class="text-muted">{{ $users->total() }} user{{ $users->total() === 1 ? '' : 's' }} found</small>
        </div>
        <a class="btn btn-primary" href="{{ route('users.create') }}">
            <i class="fa fa-plus me-1"></i>
            New User
        </a>
    </div>

    <div class="card shadow-sm mb-3 users-filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row g-3">
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <label class="form-label" for="search">Search</label>
                        <input
                            class="form-control"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] ?? '' }}"
                            placeholder="Name, username, email, mobile">
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <label class="form-label" for="designation_id">Designation</label>
                        <select class="form-select" id="designation_id" name="designation_id">
                            <option value="">All designations</option>
                            @foreach ($filterData['designations'] as $designation)
                            <option value="{{ $designation->id }}" @selected(($filters['designation_id'] ?? '' )==$designation->id)>{{ $designation->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label" for="district_id">District</label>
                        <select class="form-select" id="district_id" name="district_id">
                            <option value="">All districts</option>
                            @foreach ($filterData['districts'] as $district)
                            <option value="{{ $district->id }}" @selected(($filters['district_id'] ?? '' )==$district->id)>
                                {{ $district->district_name }} ({{ $district->division_name }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <label class="form-label" for="reporting_to_user_id">Reports to</label>
                        <select class="form-select" id="reporting_to_user_id" name="reporting_to_user_id">
                            <option value="">All reporting users</option>
                            @foreach ($filterData['reportingUsers'] as $reportingUser)
                            <option value="{{ $reportingUser->id }}" @selected(($filters['reporting_to_user_id'] ?? '' )==$reportingUser->id)>{{ $reportingUser->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <label class="form-label" for="is_active">Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="">All</option>
                            <option value="1" @selected(($filters['is_active'] ?? '' )==='1' )>Active</option>
                            <option value="0" @selected(($filters['is_active'] ?? '' )==='0' )>Inactive</option>
                        </select>
                    </div>

                    <div class="col-xl-1 col-lg-4 col-md-6">
                        <label class="form-label" for="per_page">Rows</label>
                        <select class="form-select" id="per_page" name="per_page">
                            @foreach ($filterData['perPageOptions'] as $option)
                            <option value="{{ $option }}" @selected(($filters['per_page'] ?? 15)==$option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-3 col-lg-4 col-md-6 d-flex align-items-end gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-filter me-1"></i>
                            Filter
                        </button>
                        <a class="btn btn-light" href="{{ route('users.index') }}">
                            <i class="fa fa-rotate-left me-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Designation</th>
                        <th>District</th>
                        <th>Reports to</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">
                                {{ $user->username }}
                                @if ($user->employee_code)
                                | {{ $user->employee_code }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            <small class="text-muted">{{ $user->mobile ?: 'No mobile' }}</small>
                        </td>
                       
                        <td>
                            <div>{{ $user->designation_name ?: '-' }}</div>
                            @if ($user->hierarchy_level)
                            <small class="text-muted">Level {{ $user->hierarchy_level }}</small>
                            @endif
                        </td>
                        <td>
                            <div>{{ $user->district_name ?: '-' }}</div>
                            @if ($user->division_name)
                            <small class="text-muted">{{ $user->division_name }}</small>
                            @endif
                        </td>
                        <td>{{ $user->reporting_user_name ?: '-' }}</td>
                        <td>
                            @if ($user->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y') : '-' }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-light" href="{{ route('users.edit', $user) }}">
                                <i class="fa fa-pen me-1"></i>
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center text-muted py-4" colspan="8">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
        <div class="card-body border-top">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
