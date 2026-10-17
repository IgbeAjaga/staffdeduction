@extends('layouts.app')

@section('title', 'Audit Trail Detail')
@section('page-title', 'Audit Trail Detail')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            Audit Trail — {{ $deduction->deduction_id }}
                        </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('audits.index') }}">Audits</a></li>
                                <li class="breadcrumb-item active">{{ $deduction->deduction_id }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('deductions.show', $deduction) }}" class="btn btn-outline-primary btn-sm">
                    View Deduction
                </a>
                <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    {{ $deduction->staff_name }} — ₦{{ number_format($deduction->amount, 2) }}
                </h4>
            </div>
            <div class="card-body">
                @forelse($audits as $audit)
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
                            <small class="text-muted">{{ $audit->created_at->format('d M Y h:i:s A') }}</small>
                            @if($audit->description)
                                <p class="mb-0 mt-50">{{ $audit->description }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No audit records for this deduction.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection