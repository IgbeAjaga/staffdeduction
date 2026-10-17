@extends('layouts.app')

@section('title', 'Soft Delete Log Detail')
@section('page-title', 'Soft Delete Log Detail')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row mb-2">
            <div class="content-header-left col-md-9 col-12">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Soft Delete Log Detail</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('soft-delete-logs.index') }}">Soft Delete Logs</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 text-end">
                <a href="{{ route('soft-delete-logs.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    {{ class_basename($softDeleteLog->model_type) }} #{{ $softDeleteLog->model_id }}
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive mb-2">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="200">Model</th>
                                <td>{{ class_basename($softDeleteLog->model_type) }}</td>
                            </tr>
                            <tr>
                                <th>Record ID</th>
                                <td>#{{ $softDeleteLog->model_id }}</td>
                            </tr>
                            <tr>
                                <th>Deleted By</th>
                                <td>{{ $softDeleteLog->deleter?->name ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <th>Deleted At</th>
                                <td>{{ $softDeleteLog->created_at->format('d M Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <th>Reason</th>
                                <td>{{ $softDeleteLog->reason ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5 class="mb-1">Deleted Data Snapshot</h5>
                <pre class="bg-light p-2 rounded" style="max-height: 400px; overflow: auto; font-size: 12px;">{{ json_encode($softDeleteLog->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

    </div>
</div>
@endsection