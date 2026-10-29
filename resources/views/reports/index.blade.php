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

        <div class="alert alert-info mb-2">
            One row per <strong>salary number</strong>.
            <strong>Applied</strong> = total ever deducted (status applied).
            <strong>Remaining</strong> = total approved but not yet applied.
        </div>

        <!-- FILTER CARD -->
        <div class="card mb-2">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-1 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="form-control" placeholder="Name or salary number">
                        </div>
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
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-12 mt-1 d-flex flex-wrap gap-1">
                            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
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
                        <h6 class="text-muted mb-0">Total Amount (filtered deductions)</h6>
                        <h3 class="fw-bolder mb-0">₦{{ number_format($stats['total_amount'] ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card">
                    <div class="card-body py-1">
                        <h6 class="text-muted mb-0">Staff in report</h6>
                        <h3 class="fw-bolder mb-0">{{ $staffRows->total() }}</h3>
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

        <!-- TABLE: one row per salary number -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 70vh; overflow: auto;">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th>#</th>
                                <th>Salary No</th>
                                <th>Staff Name</th>
                                <th>Branch</th>
                                <th>Section</th>
                                <th>Department</th>
                                <th class="text-end">Deducted</th>
                                <th class="text-end">To be Deducted</th>
                                <th class="text-end">Pending Approval</th>
                                <th class="text-center">Records</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffRows as $row)
                                <tr>
                                    <td>
                                        {{ $loop->iteration + ($staffRows->currentPage() - 1) * $staffRows->perPage() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('deductions.index', ['search' => $row['salary_number']]) }}">
                                            <strong>{{ $row['salary_number'] }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $row['staff_name'] }}</td>
                                    <td>{{ $row['branch'] ?? '—' }}</td>
                                    <td>{{ $row['section'] ?? '—' }}</td>
                                    <td>{{ $row['department'] ?? '—' }}</td>
                                    <td class="text-end text-info fw-bold">
                                        ₦{{ number_format($row['applied'], 2) }}
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        ₦{{ number_format($row['remaining'], 2) }}
                                    </td>
                                    <td class="text-end text-danger fw-bold">
                                        ₦{{ number_format($row['pending'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-secondary">{{ $row['deduction_count'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">No staff found for the selected filters.</td>
                                </tr>
                            @endforelse
                                                </tbody>
                        @if($staffRows->count())
                            @php
                                // Use the all-pages totals from the controller if available, otherwise sum the current page
                                $tApplied   = $totals['applied']         ?? $staffRows->getCollection()->sum('applied');
                                $tRemaining = $totals['remaining']       ?? $staffRows->getCollection()->sum('remaining');
                                $tPending   = $totals['pending']         ?? $staffRows->getCollection()->sum('pending');
                                $tRecords   = $totals['deduction_count'] ?? $staffRows->getCollection()->sum('deduction_count');
                            @endphp
                            <tfoot class="table-dark fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">
                                        TOTAL {{ isset($totals) ? '(all ' . $staffRows->total() . ' staff)' : '(this page)' }}
                                    </td>
                                    <td class="text-end">₦{{ number_format($tApplied, 2) }}</td>
                                    <td class="text-end">₦{{ number_format($tRemaining, 2) }}</td>
                                    <td class="text-end">₦{{ number_format($tPending, 2) }}</td>
                                    <td class="text-center">{{ $tRecords }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <div class="mt-3">
                    {{ $staffRows->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection