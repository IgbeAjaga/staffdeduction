@extends('layouts.app')

@section('title', 'Deduction Details')
@section('page-title', 'Deduction Details')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- Header / Breadcrumbs -->
        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            {{ $deduction->deduction_id }}
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('deductions.index') }}">Deductions</a>
                                </li>
                                <li class="breadcrumb-item active">View</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('deductions.index') }}" class="btn btn-outline-secondary btn-sm">
                    ← Back
                </a>
                <a href="{{ route('deductions.edit', $deduction) }}" class="btn btn-dark btn-sm">
                    Edit
                </a>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">

            <!-- LEFT COLUMN - Details -->
            <div class="col-lg-8">

                <!-- Status Badge -->
                <div class="mb-2">
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
                    <span class="badge bg-light-{{ $badge }} text-{{ $badge }} fs-6 px-2 py-1">
                        {{ ucfirst($deduction->status) }}
                    </span>
                    <span class="badge bg-light-secondary ms-1">
                        {{ ucfirst($deduction->category) }}
                    </span>
                </div>

                <!-- Main Details Card -->
                <div class="card mb-2">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Deduction Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th width="220">Deduction ID</th>
                                        <td><code>{{ $deduction->deduction_id }}</code></td>
                                    </tr>
                                    <tr>
                                        <th>Staff Name</th>
                                        <td>{{ $deduction->staff_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Salary Number</th>
                                        <td>{{ $deduction->salary_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ $deduction->branch?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Section</th>
                                        <td>{{ $deduction->section?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $deduction->department?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Job Title</th>
                                        <td>{{ $deduction->jobTitle?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deduction Type</th>
                                        <td>{{ $deduction->deductionType?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td class="fw-bold fs-5">₦{{ number_format($deduction->amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Plan</th>
                                        <td>{{ ucfirst($deduction->category) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deduction Date</th>
                                        <td>{{ $deduction->deduction_date?->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Effective Date</th>
                                        <td>{{ $deduction->effective_date?->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Reason</th>
                                        <td>{{ $deduction->reason }}</td>
                                    </tr>
                                    @if($deduction->remarks)
                                        <tr>
                                            <th>Remarks</th>
                                            <td>{{ $deduction->remarks }}</td>
                                        </tr>
                                    @endif
                                    @if($deduction->supporting_document)
    <tr>
        <th>Supporting Document</th>
        <td>
            <a href="{{ route('deductions.document', $deduction) }}"
               target="_blank"
               class="btn btn-sm btn-outline-primary">
                View Document
            </a>
        </td>
    </tr>
@endif
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{ $deduction->creator?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $deduction->created_at?->format('d M Y h:i A') }}</td>
                                    </tr>
                                    @if($deduction->approved_by)
                                        <tr>
                                            <th>Approved By</th>
                                            <td>{{ $deduction->approved_by }}</td>
                                        </tr>
                                    @endif
                                    @if($deduction->approval_date)
                                        <tr>
                                            <th>Approval Date</th>
                                            <td>{{ $deduction->approval_date?->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                    @if($deduction->applied_date)
                                        <tr>
                                            <th>Applied Date</th>
                                            <td>{{ $deduction->applied_date?->format('d M Y') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Installments (if any) -->
                @if($deduction->category === 'installment' && $deduction->installments->count())
                    <div class="card mb-2">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Installment Schedule</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Payroll Period</th>
                                            <th>Period Start</th>
                                            <th>Period End</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deduction->installments as $inst)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $inst->payroll_period }}</td>
                                                <td>{{ $inst->period_start?->format('d M Y') ?? '—' }}</td>
                                                <td>{{ $inst->period_end?->format('d M Y') ?? '—' }}</td>
                                                <td class="text-end fw-bold">₦{{ number_format($inst->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="4" class="text-end">Total</td>
                                            <td class="text-end">₦{{ number_format($deduction->installments->sum('amount'), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Audit Trail -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Audit Trail</h4>
                    </div>
                    <div class="card-body">
                        @forelse($deduction->audits as $audit)
                            <div class="d-flex mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="me-2">
                                    <span class="badge bg-light-primary rounded-circle p-1">
                                        <i data-feather="activity" class="font-small-3"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong>{{ ucfirst($audit->action) }}</strong>
                                    <span class="text-muted">by {{ $audit->user?->name ?? 'System' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $audit->created_at->format('d M Y h:i A') }}</small>
                                    @if($audit->description)
                                        <p class="mb-0 mt-50 text-muted">{{ $audit->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No audit records yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN - Actions -->
            <div class="col-lg-4">

                <div class="card mb-2">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Workflow Actions</h4>
                    </div>
                    <div class="card-body">

                        @if($deduction->status === 'pending')
                            <!-- Approve -->
                            <form action="{{ route('deductions.approve', $deduction) }}" method="POST" class="mb-2">
                                @csrf
                                <div class="mb-1">
                                    <label class="form-label">Approved By (Full Name + Position)</label>
                                    <input type="text"
                                           name="approved_by"
                                           class="form-control"
                                           placeholder="e.g. John Doe (HR Manager)"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i data-feather="check-circle"></i> Approve
                                </button>
                            </form>

                            <!-- Reject -->
                            <form action="{{ route('deductions.reject', $deduction) }}" method="POST" class="mb-2">
                                @csrf
                                <div class="mb-1">
                                    <label class="form-label">Rejection Reason (optional)</label>
                                    <textarea name="reason" rows="2" class="form-control"
                                              placeholder="Why is this being rejected?"></textarea>
                                </div>
                                <button type="submit"
                                        class="btn btn-danger w-100"
                                        onclick="return confirm('Reject this deduction?')">
                                    <i data-feather="x-circle"></i> Reject
                                </button>
                            </form>
                        @endif

                        @if($deduction->status === 'approved')
                            <form action="{{ route('deductions.apply', $deduction) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit"
                                        class="btn btn-info w-100"
                                        onclick="return confirm('Apply this deduction to payroll?')">
                                    <i data-feather="check-square"></i> Apply to Payroll
                                </button>
                            </form>
                        @endif

                        @if(in_array($deduction->status, ['pending', 'approved']))
                            <form action="{{ route('deductions.cancel', $deduction) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="btn btn-outline-secondary w-100"
                                        onclick="return confirm('Cancel this deduction?')">
                                    <i data-feather="slash"></i> Cancel Deduction
                                </button>
                            </form>
                        @endif

                        @if(!in_array($deduction->status, ['pending', 'approved']))
                            <p class="text-muted mb-0 text-center">
                                No further actions available for this status.
                            </p>
                        @endif

                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Quick Info</h4>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">
                            <strong>Status:</strong>
                            <span class="badge bg-light-{{ $badge }} text-{{ $badge }}">
                                {{ ucfirst($deduction->status) }}
                            </span>
                        </p>
                        <p class="mb-1">
                            <strong>Amount:</strong>
                            ₦{{ number_format($deduction->amount, 2) }}
                        </p>
                        <p class="mb-1">
                            <strong>Category:</strong>
                            {{ ucfirst($deduction->category) }}
                        </p>
                        <p class="mb-0">
                            <strong>Created:</strong>
                            {{ $deduction->created_at?->diffForHumans() }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection