@extends('layouts.app')

@section('title', 'Payroll Due Report')
@section('page-title', 'Payroll Due Report')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <h2 class="content-header-title float-start mb-0">Payroll Due Report</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Payroll Due</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            This report lists <strong>amounts due for salary deduction</strong> by period.
            Installments appear in their payroll month; one-time items appear in the effective-date month.
            Only <strong>approved</strong> deductions are included by default (send this to the salary accountant).
        </div>

        <!-- FILTERS -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Month (payroll)</label>
                            <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">All Branches</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" @selected(($filters['branch_id'] ?? '') == $b->id)>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Section</label>
                            <select name="section_id" class="form-select">
                                <option value="">All Sections</option>
                                @foreach($sections as $s)
                                    <option value="{{ $s->id }}" @selected(($filters['section_id'] ?? '') == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Staff / Salary No / ID</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                   class="form-control" placeholder="Name or salary number">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-2">
                                <input type="checkbox" name="include_applied" value="1" class="form-check-input"
                                       id="include_applied" @checked(!empty($filters['include_applied']))>
                                <label class="form-check-label" for="include_applied">Include already applied</label>
                            </div>
                        </div>
                        <div class="col-12 mt-1 d-flex flex-wrap gap-1">
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i data-feather="filter"></i> Filter
                            </button>
                            <a href="{{ route('reports.payroll-due') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <a href="{{ route('reports.payroll-due.excel', request()->query()) }}"
                               class="btn btn-success btn-sm">Export Excel / CSV</a>
                            <a href="{{ route('reports.payroll-due.pdf', request()->query()) }}"
                               target="_blank" class="btn btn-danger btn-sm">Export PDF / Print</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- STATS -->
        <div class="row mb-2">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Total Amount Due</h6>
                        <h3 class="fw-bolder mb-0 text-primary">₦{{ number_format($stats['total_due'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Staff Count</h6>
                        <h3 class="fw-bolder mb-0">{{ $stats['staff_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Line Items</h6>
                        <h3 class="fw-bolder mb-0">{{ $stats['line_count'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(!empty($filters['month']) && $stats['line_count'] > 0)
    <div class="card mb-2 border-success">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <strong>Ready for payroll</strong>
                <span class="text-muted ms-1">
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $filters['month'])->format('F Y') }}
                    · {{ $stats['line_count'] }} line(s)
                    · ₦{{ number_format($stats['total_due'], 2) }}
                </span>
            </div>

            <form action="{{ route('reports.payroll-due.apply') }}" method="POST"
                  onsubmit="return confirm('Mark all APPROVED deductions due in this month as APPLIED?\n\nOnly do this after the salary accountant has deducted the money.')">
                @csrf
                <input type="hidden" name="month" value="{{ $filters['month'] }}">
                <input type="hidden" name="branch_id" value="{{ $filters['branch_id'] ?? '' }}">
                <input type="hidden" name="section_id" value="{{ $filters['section_id'] ?? '' }}">
                <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">

                <button type="submit" class="btn btn-success">
                    <i data-feather="check-square"></i>
                    Apply all shown to payroll
                </button>
            </form>
        </div>
    </div>
@elseif(empty($filters['month']))
    <div class="alert alert-secondary">
        Select a <strong>month</strong> to load dues and enable the single “Apply all” action.
    </div>
@endif

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Salary No</th>
                                <th>Branch</th>
                                <th>Section</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Payroll Period</th>
                                <th class="text-end">Amount Due</th>
                                <th>Deduction ID</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lines as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row['staff_name'] }}</td>
                                    <td>{{ $row['salary_number'] }}</td>
                                    <td>{{ $row['branch'] ?? '—' }}</td>
                                    <td>{{ $row['section'] ?? '—' }}</td>
                                    <td>{{ $row['type'] ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-light-secondary">{{ ucfirst($row['category']) }}</span>
                                    </td>
                                    <td>{{ $row['payroll_period'] ?? '—' }}</td>
                                    <td class="text-end fw-bold">₦{{ number_format($row['amount_due'], 2) }}</td>
                                    <td><code>{{ $row['deduction_id'] }}</code></td>
                                    <td>
                                        @php
                                            $badge = match($row['status']) {
                                                'approved' => 'success',
                                                'applied'  => 'info',
                                                default    => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge bg-light-{{ $badge }} text-{{ $badge }}">
                                            {{ ucfirst($row['status']) }}
                                        </span>
                                    </td>
                                    <td class="small">{{ \Illuminate\Support\Str::limit($row['reason'], 40) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        No amounts due for the selected filters.
                                        Try approving pending deductions or changing the month.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($lines->count())
                            <tfoot class="table-dark fw-bold">
                                <tr>
                                    <td colspan="8" class="text-end">TOTAL DUE</td>
                                    <td class="text-end">₦{{ number_format($stats['total_due'], 2) }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection