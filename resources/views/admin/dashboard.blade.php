@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- Dashboard Overview Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title mb-0">Overview</h4>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <!-- Total Deductions -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Total Deductions
                                    </h6>
                                    <h2 class="fw-bold mb-0">
                                        {{ \App\Models\Deduction::count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-primary p-2">
                                    <i data-feather="file-text" class="font-medium-3 text-primary"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('deductions.index') }}" class="text-primary fw-semibold">
                                    View Records →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Pending
                                    </h6>
                                    <h2 class="fw-bold mb-0 text-warning">
                                        {{ \App\Models\Deduction::where('status', 'pending')->count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-warning p-2">
                                    <i data-feather="clock" class="font-medium-3 text-warning"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('deductions.index', ['status' => 'pending']) }}" class="text-warning fw-semibold">
                                    View Pending →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Approved
                                    </h6>
                                    <h2 class="fw-bold mb-0 text-success">
                                        {{ \App\Models\Deduction::where('status', 'approved')->count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-success p-2">
                                    <i data-feather="check-circle" class="font-medium-3 text-success"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('deductions.index', ['status' => 'approved']) }}" class="text-success fw-semibold">
                                    View Approved →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Applied -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Applied to Payroll
                                    </h6>
                                    <h2 class="fw-bold mb-0 text-info">
                                        {{ \App\Models\Deduction::where('status', 'applied')->count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-info p-2">
                                    <i data-feather="check-square" class="font-medium-3 text-info"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('deductions.index', ['status' => 'applied']) }}" class="text-info fw-semibold">
                                    View Applied →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Total Amount
                                    </h6>
                                    <h2 class="fw-bold mb-0">
                                        {{ number_format(\App\Models\Deduction::sum('amount'), 2) }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-primary p-2">
                                    <i data-feather="dollar-sign" class="font-medium-3 text-primary"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('reports.index') }}" class="text-primary fw-semibold">
                                    View Reports →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Branches -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Branches
                                    </h6>
                                    <h2 class="fw-bold mb-0">
                                        {{ \App\Models\Branch::count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-secondary p-2">
                                    <i data-feather="map-pin" class="font-medium-3 text-secondary"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('branches.index') }}" class="text-secondary fw-semibold">
                                    Manage Branches →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Departments -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Departments
                                    </h6>
                                    <h2 class="fw-bold mb-0">
                                        {{ \App\Models\Department::count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-secondary p-2">
                                    <i data-feather="briefcase" class="font-medium-3 text-secondary"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('departments.index') }}" class="text-secondary fw-semibold">
                                    Manage Departments →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Soft Deleted -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1 text-uppercase">
                                        Soft Deleted
                                    </h6>
                                    <h2 class="fw-bold mb-0 text-danger">
                                        {{ \App\Models\SoftDeleteLog::count() }}
                                    </h2>
                                </div>
                                <div class="avatar bg-light-danger p-2">
                                    <i data-feather="trash-2" class="font-medium-3 text-danger"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pt-0">
                                <a href="{{ route('soft-delete-logs.index') }}" class="text-danger fw-semibold">
                                    View Logs →
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Recent Deductions -->
        <div class="card mt-3">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Recent Deductions</h4>
                <a href="{{ route('deductions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Deduction ID</th>
                                <th>Staff Name</th>
                                <th>Branch</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\App\Models\Deduction::with('branch')->latest()->take(8)->get() as $deduction)
                                <tr>
                                    <td><code>{{ $deduction->deduction_id }}</code></td>
                                    <td>{{ $deduction->staff_name }}</td>
                                    <td>{{ $deduction->branch?->name ?? '—' }}</td>
                                    <td>{{ number_format($deduction->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $badge = match($deduction->status) {
                                                'pending'   => 'warning',
                                                'approved'  => 'success',
                                                'rejected'  => 'danger',
                                                'applied'   => 'info',
                                                'cancelled' => 'secondary',
                                                default     => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-light-{{ $badge }} text-{{ $badge }}">
                                            {{ ucfirst($deduction->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $deduction->deduction_date?->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('deductions.show', $deduction) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No deductions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection