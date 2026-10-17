@extends('layouts.app')

@section('title', 'Super Super Admin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        @include('dashboards.partials.header', ['title' => 'Super Super Admin Dashboard'])

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="content-body">
            <section>
                <div class="row">
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Total Deductions', 'value' => \App\Models\Deduction::count(),
                        'link' => route('deductions.index'), 'linkText' => 'View All',
                        'color' => 'primary', 'icon' => 'file-text',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Pending', 'value' => \App\Models\Deduction::where('status', 'pending')->count(),
                        'link' => route('deductions.index', ['status' => 'pending']), 'linkText' => 'Review Pending',
                        'color' => 'warning', 'icon' => 'clock',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Approved', 'value' => \App\Models\Deduction::where('status', 'approved')->count(),
                        'link' => route('deductions.index', ['status' => 'approved']), 'linkText' => 'View Approved',
                        'color' => 'success', 'icon' => 'check-circle',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Total Amount', 'value' => '₦' . number_format(\App\Models\Deduction::sum('amount'), 2),
                        'link' => route('reports.index'), 'linkText' => 'View Reports',
                        'color' => 'info', 'icon' => 'dollar-sign',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Applied to Payroll', 'value' => \App\Models\Deduction::where('status', 'applied')->count(),
                        'link' => route('deductions.index', ['status' => 'applied']), 'linkText' => 'View Applied',
                        'color' => 'primary', 'icon' => 'check-square',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Audit Records', 'value' => \App\Models\DeductionAudit::count(),
                        'link' => route('audits.index'), 'linkText' => 'View Audit Trail',
                        'color' => 'warning', 'icon' => 'activity',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Soft Deleted', 'value' => \App\Models\SoftDeleteLog::count(),
                        'link' => route('soft-delete-logs.index'), 'linkText' => 'View Logs',
                        'color' => 'danger', 'icon' => 'trash-2',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Total Users', 'value' => \App\Models\User::count(),
                        'link' => route('superadmin.users.index'), 'linkText' => 'Manage Users',
                        'color' => 'primary', 'icon' => 'users',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Branches', 'value' => \App\Models\Branch::count(),
                        'link' => route('branches.index'), 'linkText' => 'Manage Branches',
                        'color' => 'secondary', 'icon' => 'map-pin',
                    ])
                </div>

                {{-- Log Management: visible only to users who pass the clear-logs gate --}}
                @can('clear-logs')
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-danger">
                            <div class="card-header">
                                <h5 class="card-title mb-0 text-danger">
                                    <i data-feather="alert-triangle"></i> Log Management
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Only the Super Super Admin can clear logs. Clearing is permanent and cannot be undone.
                                </p>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <form action="{{ route('audits.clear') }}" method="POST"
                                              onsubmit="return confirm('Permanently clear ALL audit records? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100 py-2">
                                                <i data-feather="trash"></i>
                                                Clear All Audit Logs ({{ \App\Models\DeductionAudit::count() }})
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('soft-delete-logs.clear') }}" method="POST"
                                              onsubmit="return confirm('Permanently clear ALL soft delete logs? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger w-100 py-2">
                                                <i data-feather="trash"></i>
                                                Clear All Soft Delete Logs ({{ \App\Models\SoftDeleteLog::count() }})
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    To clear a single record, open the log page and use the delete button on that row.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                {{-- Quick Access --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Quick Access</h5></div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('deductions.index') }}" class="btn btn-outline-primary w-100 py-2">
                                            <i data-feather="file-text"></i> All Deductions
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('reports.index') }}" class="btn btn-outline-success w-100 py-2">
                                            <i data-feather="bar-chart-2"></i> Reports
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('audits.index') }}" class="btn btn-outline-warning w-100 py-2">
                                            <i data-feather="activity"></i> Audit Trails
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('soft-delete-logs.index') }}" class="btn btn-outline-danger w-100 py-2">
                                            <i data-feather="trash-2"></i> Soft Delete Logs
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary w-100 py-2">
                                            <i data-feather="map-pin"></i> Branches
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary w-100 py-2">
                                            <i data-feather="briefcase"></i> Departments
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('job-titles.index') }}" class="btn btn-outline-secondary w-100 py-2">
                                            <i data-feather="award"></i> Job Titles
                                        </a>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-info w-100 py-2">
                                            <i data-feather="shield"></i> User Management
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('dashboards.partials.recent-deductions')
            </section>
        </div>
    </div>
</div>
@endsection