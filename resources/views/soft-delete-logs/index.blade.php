@extends('layouts.app')

@section('title', 'Soft Delete Logs')
@section('page-title', 'Soft Delete Logs')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Soft Delete Logs</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Soft Delete Logs</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-warning">
            Only Superadmin can view this page. All soft-deleted records are permanently logged here.
        </div>

        <!-- FILTER -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Model Type</label>
                            <select name="model_type" class="form-select">
                                <option value="">All Models</option>
                                <option value="App\Models\Deduction" @selected(request('model_type') == 'App\Models\Deduction')>Deduction</option>
                                <option value="App\Models\Branch" @selected(request('model_type') == 'App\Models\Branch')>Branch</option>
                                <option value="App\Models\Department" @selected(request('model_type') == 'App\Models\Department')>Department</option>
                                <option value="App\Models\JobTitle" @selected(request('model_type') == 'App\Models\JobTitle')>Job Title</option>
                                <option value="App\Models\DeductionType" @selected(request('model_type') == 'App\Models\DeductionType')>Deduction Type</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i data-feather="filter"></i> Filter
                            </button>
                            <a href="{{ route('soft-delete-logs.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Model</th>
                                <th>Record ID</th>
                                <th>Deleted By</th>
                                <th>Reason</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        @if(method_exists($logs, 'currentPage'))
                                            {{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ class_basename($log->model_type) }}</td>
                                    <td>#{{ $log->model_id }}</td>
                                    <td>{{ $log->deleter?->name ?? 'Unknown' }}</td>
                                    <td>{{ $log->reason ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('soft-delete-logs.show', $log) }}"
                                           class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No soft delete logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(method_exists($logs, 'links'))
                        <div class="mt-3">
                            {{ $logs->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection