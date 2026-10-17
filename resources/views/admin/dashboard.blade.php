@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        @include('dashboards.partials.header', ['title' => 'Admin Dashboard'])

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
                        'title' => 'Rejected', 'value' => \App\Models\Deduction::where('status', 'rejected')->count(),
                        'link' => route('deductions.index', ['status' => 'rejected']), 'linkText' => 'View Rejected',
                        'color' => 'danger', 'icon' => 'x-circle',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Branches', 'value' => \App\Models\Branch::count(),
                        'link' => route('branches.index'), 'linkText' => 'Manage Branches',
                        'color' => 'secondary', 'icon' => 'map-pin',
                    ])
                    @include('dashboards.partials.stat-card', [
                        'title' => 'Departments', 'value' => \App\Models\Department::count(),
                        'link' => route('departments.index'), 'linkText' => 'Manage Departments',
                        'color' => 'secondary', 'icon' => 'briefcase',
                    ])
                </div>

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
                                        <a href="{{ route('deductions.create') }}" class="btn btn-outline-info w-100 py-2">
                                            <i data-feather="plus-circle"></i> New Deduction
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