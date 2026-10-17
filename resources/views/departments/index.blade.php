@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Departments</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Departments</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus"></i> New Department
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="60">#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $department)
                                <tr>
                                    <td>
                                        @if(method_exists($departments, 'currentPage'))
                                            {{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $department->name }}
                                        @if(method_exists($department, 'trashed') && $department->trashed())
                                            <span class="badge bg-danger ms-1">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $department->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(method_exists($department, 'trashed') && $department->trashed())
                                            <form action="{{ route('departments.restore', $department->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                            </form>
                                        @else
                                            <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-dark">Edit</a>
                                            <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this department?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No departments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(method_exists($departments, 'links'))
                        <div class="mt-3">
                            {{ $departments->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection