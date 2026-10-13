@extends('layouts.app')

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper container-xxl p-0">

        <!-- Header -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">
                            Admin User Approvals
                        </h2>

                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    User Approvals
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mx-2">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $user->name }}</td>

                                <td>{{ $user->email }}</td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>

                                <td>
                                    @if($user->is_approved)
                                        <span class="badge bg-success">
                                            Approved
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if(!$user->is_approved)
                                        <form method="POST"
                                              action="{{ route('superadmin.users.approve', $user) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    onclick="return confirm('Approve this user?')"
                                                    class="btn btn-sm btn-success">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST"
                                              action="{{ route('superadmin.users.disapprove', $user) }}"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    onclick="return confirm('Disapprove this user?')"
                                                    class="btn btn-sm btn-danger">
                                                Disapprove
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No admin users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $users->links() }}
            </div>
        </div>

    </div>
</div>
<!-- END: Content-->
@endsection
