@extends('layouts.app')

@section('title', 'Audit Trails')
@section('page-title', 'Audit Trails')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Audit Trails</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Audit Trails</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                @foreach(['created','updated','approved','rejected','applied','cancelled'] as $action)
                                    <option value="{{ $action }}" @selected(request('action') == $action)>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
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
                            <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                                <th>Deduction ID</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audits as $audit)
                                <tr>
                                    <td>
                                        @if(method_exists($audits, 'currentPage'))
                                            {{ $loop->iteration + ($audits->currentPage() - 1) * $audits->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>{{ $audit->created_at->format('d M Y h:i A') }}</td>
                                    <td>
                                        @if($audit->deduction)
                                            <a href="{{ route('audits.show', $audit->deduction) }}">
                                                <code>{{ $audit->deduction->deduction_id }}</code>
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light-primary">
                                            {{ ucfirst($audit->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $audit->user?->name ?? 'System' }}</td>
                                    <td>{{ $audit->description ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No audit records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(method_exists($audits, 'links'))
                        <div class="mt-3">
                            {{ $audits->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection