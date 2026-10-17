@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Deduction Reports</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER CARD -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">All Branches</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Section</label>
                            <select name="section_id" class="form-select">
                                <option value="">All Sections</option>
                                @foreach($sections ?? [] as $section)
                                    <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>
                                        {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach(['pending','approved','rejected','applied','cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') == $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All</option>
                                <option value="one_time" @selected(request('category') == 'one_time')>One Time</option>
                                <option value="installment" @selected(request('category') == 'installment')>Installment</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-12 mt-1 d-flex flex-wrap gap-1">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i data-feather="filter"></i> Filter
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <a href="{{ route('reports.export.excel') }}?{{ http_build_query(request()->query()) }}"
                               class="btn btn-success btn-sm">Export Excel</a>
                            <a href="{{ route('reports.export.pdf') }}?{{ http_build_query(request()->query()) }}"
                               class="btn btn-danger btn-sm">Export PDF</a>
                            <a href="{{ route('reports.print') }}?{{ http_build_query(request()->query()) }}"
                               target="_blank" class="btn btn-outline-primary btn-sm">Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- STATS -->
        <div class="row mb-2">
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Total Amount</h6>
                        <h3 class="fw-bolder mb-0">₦{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Total Records</h6>
                        <h3 class="fw-bolder mb-0">{{ $stats['total_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Pending</h6>
                        <h3 class="fw-bolder mb-0 text-warning">{{ $stats['pending_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Approved</h6>
                        <h3 class="fw-bolder mb-0 text-success">{{ $stats['approved_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Applied</h6>
                        <h3 class="fw-bolder mb-0 text-info">{{ $stats['applied_count'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>#</th>
                                <th>Deduction ID</th>
                                <th>Staff Name</th>
                                <th>Branch</th>
                                <th>Section</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deductions as $deduction)
                                <tr>
                                    <td>
                                        @if(method_exists($deductions, 'currentPage'))
                                            {{ $loop->iteration + ($deductions->currentPage() - 1) * $deductions->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('deductions.show', $deduction) }}">
                                            <code>{{ $deduction->deduction_id }}</code>
                                        </a>
                                    </td>
                                    <td>{{ $deduction->staff_name }}</td>
                                    <td>{{ $deduction->branch?->name ?? '—' }}</td>
                                    <td>{{ $deduction->section?->name ?? '—' }}</td>
                                    <td>{{ $deduction->department?->name ?? '—' }}</td>
                                    <td>{{ $deduction->deductionType?->name ?? '—' }}</td>
                                    <td class="fw-bold">₦{{ number_format($deduction->amount, 2) }}</td>
                                    <td>{{ ucfirst($deduction->category) }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($deductions, 'links'))
                    <div class="mt-3">
                        {{ $deductions->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection