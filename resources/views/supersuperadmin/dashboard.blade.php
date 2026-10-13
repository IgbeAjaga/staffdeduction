@extends('layouts.app')

@section('title', 'Super Super Admin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">

        {{-- Page Header --}}
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            Super Super Admin Dashboard
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Super Super Admin Dashboard
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Body --}}
        <div class="content-body">
            <section>
                <div class="row">

                    {{-- Total Deductions --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Deductions</h6>
                                        <h2 class="fw-bolder mb-1">
                                            {{ \App\Models\Deduction::count() }}
                                        </h2>
                                        <a href="{{ route('deductions.index') }}" class="text-primary">
                                            View All →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-primary">
                                        <div class="avatar-content">
                                            <i data-feather="file-text"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pending Deductions --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Pending</h6>
                                        <h2 class="fw-bolder mb-1 text-warning">
                                            {{ \App\Models\Deduction::where('status', 'pending')->count() }}
                                        </h2>
                                        <a href="{{ route('deductions.index', ['status' => 'pending']) }}" class="text-warning">
                                            Review Pending →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-warning">
                                        <div class="avatar-content">
                                            <i data-feather="clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approved Deductions --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Approved</h6>
                                        <h2 class="fw-bolder mb-1 text-success">
                                            {{ \App\Models\Deduction::where('status', 'approved')->count() }}
                                        </h2>
                                        <a href="{{ route('deductions.index', ['status' => 'approved']) }}" class="text-success">
                                            View Approved →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-success">
                                        <div class="avatar-content">
                                            <i data-feather="check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Amount --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Amount</h6>
                                        <h2 class="fw-bolder mb-1 text-info">
                                            ₦{{ number_format(\App\Models\Deduction::sum('amount'), 2) }}
                                        </h2>
                                        <a href="{{ route('reports.index') }}" class="text-info">
                                            View Reports →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-info">
                                        <div class="avatar-content">
                                            <i data-feather="dollar-sign"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Applied to Payroll --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Applied to Payroll</h6>
                                        <h2 class="fw-bolder mb-1 text-primary">
                                            {{ \App\Models\Deduction::where('status', 'applied')->count() }}
                                        </h2>
                                        <a href="{{ route('deductions.index', ['status' => 'applied']) }}" class="text-primary">
                                            View Applied →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-primary">
                                        <div class="avatar-content">
                                            <i data-feather="check-square"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Soft Delete Logs --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Soft Deleted</h6>
                                        <h2 class="fw-bolder mb-1 text-danger">
                                            {{ \App\Models\SoftDeleteLog::count() }}
                                        </h2>
                                        <a href="{{ route('soft-delete-logs.index') }}" class="text-danger">
                                            View Logs →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-danger">
                                        <div class="avatar-content">
                                            <i data-feather="trash-2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Users --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Total Users</h6>
                                        <h2 class="fw-bolder mb-1">
                                            {{ \App\Models\User::count() }}
                                        </h2>
                                        <a href="{{ route('superadmin.users.index') }}" class="text-primary">
                                            Manage Users →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-primary">
                                        <div class="avatar-content">
                                            <i data-feather="users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pending Users --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-0">Pending Users</h6>
                                        <h2 class="fw-bolder mb-1 text-warning">
                                            {{ \App\Models\User::where('is_approved', false)->count() }}
                                        </h2>
                                        <a href="{{ route('superadmin.users.index') }}" class="text-warning">
                                            Approve Users →
                                        </a>
                                    </div>
                                    <div class="avatar bg-light-warning">
                                        <div class="avatar-content">
                                            <i data-feather="user-x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Quick Access Section --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Quick Access</h5>
                            </div>
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

            </section>
        </div>

    </div>
</div>
@endsection