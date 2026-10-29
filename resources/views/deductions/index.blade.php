@extends('layouts.app')

@section('title', 'Staff Deductions')
@section('page-title', 'Staff Deductions')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- BREADCRUMB -->
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0"> All Staff Deductions</h2>
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
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="form-control" placeholder="ID / Staff / Amount">
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
                                <option value="">All sections</option>
                                @foreach($sections ?? [] as $section)
                                    <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>
                                        {{ $section->name }}
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
                            <a href="{{ route('deductions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <a href="{{ route('reports.export.excel') }}?{{ http_build_query(request()->query()) }}"
                               class="btn btn-success btn-sm">Export Excel</a>
                            <a href="{{ route('reports.export.pdf') }}?{{ http_build_query(request()->query()) }}"
                               class="btn btn-danger btn-sm">Export PDF</a>
                            <a href="{{ route('deductions.create') }}" class="btn btn-primary btn-sm">
                                <i data-feather="plus"></i> New Deduction
                            </a>
                        </div>
                    </div>
                </form>
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
                <div class="table-responsive" style="max-height: 78vh; overflow: auto; border: 1px solid #dee2e6; border-radius: 6px;">
                    @if(request('group_id'))
    @php
        $groupId = request('group_id');
        $groupPending  = \App\Models\Deduction::where('group_id', $groupId)->where('status', 'pending')->count();
        $groupApproved = \App\Models\Deduction::where('group_id', $groupId)->where('status', 'approved')->count();
        $groupApplied  = \App\Models\Deduction::where('group_id', $groupId)->where('status', 'applied')->count();
        $groupTotal    = \App\Models\Deduction::where('group_id', $groupId)->count();
        $groupSum      = \App\Models\Deduction::where('group_id', $groupId)->sum('amount');
    @endphp

    <div class="card mb-2 border-primary">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-50">
                        Group: <code>{{ $groupId }}</code>
                    </h5>
                    <span class="me-1">{{ $groupTotal }} staff</span>
                    <span class="me-1">• Total: <strong>₦{{ number_format($groupSum, 2) }}</strong></span>
                    <span class="badge bg-light-warning text-warning">{{ $groupPending }} Pending</span>
                    <span class="badge bg-light-success text-success">{{ $groupApproved }} Approved</span>
                    <span class="badge bg-light-info text-info">{{ $groupApplied }} Applied</span>
                </div>

                <div class="d-flex flex-wrap gap-1 align-items-center">
                    @if($groupPending > 0)
                        {{-- Approve All --}}
                        <form action="{{ route('deductions.group.approve', $groupId) }}" method="POST" class="d-flex gap-1">
                            @csrf
                            <input type="text"
                                   name="approved_by"
                                   class="form-control form-control-sm"
                                   style="width: 180px;"
                                   placeholder="Approved by (name + position)"
                                   required>
                            <button type="submit" class="btn btn-sm btn-success"
                                    onclick="return confirm('Approve all {{ $groupPending }} pending deductions in this group?')">
                                Approve All
                            </button>
                        </form>

                        {{-- Reject All --}}
                        <form action="{{ route('deductions.group.reject', $groupId) }}" method="POST"
                              onsubmit="return confirm('Reject all pending in this group?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Reject All</button>
                        </form>
                    @endif

                    @if($groupApproved > 0)
                        <form action="{{ route('deductions.group.apply', $groupId) }}" method="POST"
                              onsubmit="return confirm('Apply all {{ $groupApproved }} approved deductions to payroll?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-info">Apply All to Payroll</button>
                        </form>
                    @endif

                    <a href="{{ route('deductions.index') }}" class="btn btn-sm btn-outline-secondary">
                        Clear Group Filter
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark text-nowrap" style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th width="160">Actions</th>
                                <th>#</th>
                                <th>Code</th>
                                <th>Salary No</th>                                
                                <th>Staff Name</th>                                
                                <th>Branch</th>
                                <th>Section</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Payment Plan</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deductions as $deduction)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('deductions.show', $deduction) }}"
                                               class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('deductions.edit', $deduction) }}"
                                               class="btn btn-sm btn-dark">Edit</a>
                                        </div>
                                    </td>
                                    <td>
                                        @if(method_exists($deductions, 'currentPage'))
                                            {{ $loop->iteration + ($deductions->currentPage() - 1) * $deductions->perPage() }}
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </td>
                                    <td>
    <code>{{ $deduction->deduction_id }}</code>
    @if($deduction->group_id)
        <br>
        <a href="{{ route('deductions.index', ['group_id' => $deduction->group_id]) }}"
           class="badge bg-light-primary text-primary text-decoration-none">
            {{ $deduction->group_id }}
        </a>
    @endif
</td>                                    
                                    <td>{{ $deduction->salary_number }}</td>
                                    <td>{{ $deduction->staff_name }}</td>                                    
                                    <td>{{ $deduction->branch?->name ?? '—' }}</td>
                                    <td>{{ $deduction->section?->name ?? '—' }}</td>
                                    <td>{{ $deduction->department?->name ?? '—' }}</td>
                                    <td>{{ $deduction->deductionType?->name ?? '—' }}</td>
                                    <td class="fw-bold">₦{{ number_format($deduction->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-light-secondary">
                                            {{ ucfirst($deduction->category) }}
                                        </span>
                                    </td>
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
                                    <td>{{ $deduction->creator?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">No deductions found.</td>
                                </tr>
                            @endforelse
                                                </tbody>
                        @if($deductions->count())
                            <tfoot class="table-dark fw-bold">
                                <tr>
                                    <td colspan="9" class="text-end">TOTAL (this page)</td>
                                    <td>₦{{ number_format($deductions->getCollection()->sum('amount'), 2) }}</td>
                                    <td colspan="4"></td>
                                </tr>
                                @isset($grandTotal)
                                    <tr>
                                        <td colspan="9" class="text-end">
                                            GRAND TOTAL (all {{ $deductions->total() }} records)
                                        </td>
                                        <td>₦{{ number_format($grandTotal, 2) }}</td>
                                        <td colspan="4"></td>
                                    </tr>
                                @endisset
                            </tfoot>
                        @endif
                    </table>

                    <div class="mt-3">
                        @if(method_exists($deductions, 'links'))
                            {{ $deductions->onEachSide(1)->links('pagination::bootstrap-5') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection