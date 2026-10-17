@extends('layouts.app')

@section('title', 'Branches')
@section('page-title', 'Branches')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Branches</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Branches</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus"></i> New Branch
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
                            @forelse($branches as $branch)
                                <tr>
                                    <td>
                                        @if(method_exists($branches, 'currentPage'))
                                            {{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $branch->name }}
                                        @if(method_exists($branch, 'trashed') && $branch->trashed())
                                            <span class="badge bg-danger ms-1">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $branch->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(method_exists($branch, 'trashed') && $branch->trashed())
                                            <form action="{{ route('branches.restore', $branch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                            </form>
                                        @else
                                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-dark">Edit</a>
                                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this branch?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No branches found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(method_exists($branches, 'links'))
                        <div class="mt-3">
                            {{ $branches->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection