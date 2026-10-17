@extends('layouts.app')

@section('title', 'Job Titles')
@section('page-title', 'Job Titles')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Job Titles</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Job Titles</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('job-titles.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus"></i> New Job Title
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
                            @forelse($jobTitles as $jobTitle)
                                <tr>
                                    <td>
                                        @if(method_exists($jobTitles, 'currentPage'))
                                            {{ $loop->iteration + ($jobTitles->currentPage() - 1) * $jobTitles->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $jobTitle->name }}
                                        @if(method_exists($jobTitle, 'trashed') && $jobTitle->trashed())
                                            <span class="badge bg-danger ms-1">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $jobTitle->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $jobTitle->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(method_exists($jobTitle, 'trashed') && $jobTitle->trashed())
                                            <form action="{{ route('job-titles.restore', $jobTitle->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                            </form>
                                        @else
                                            <a href="{{ route('job-titles.edit', $jobTitle) }}" class="btn btn-sm btn-dark">Edit</a>
                                            <form action="{{ route('job-titles.destroy', $jobTitle) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this job title?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No job titles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(method_exists($jobTitles, 'links'))
                        <div class="mt-3">
                            {{ $jobTitles->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection