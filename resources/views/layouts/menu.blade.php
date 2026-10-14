@php
    $role = auth()->user()->role ?? 'user';
    $isAdmin = in_array($role, ['admin', 'superadmin'], true);
    $isSuperAdmin = $role === 'superadmin';
@endphp

<!-- ===================================================== -->
<!-- DASHBOARD -->
<!-- ===================================================== -->
<li class="{{ Request::is('dashboard*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
        <i data-feather="home"></i>
        <span class="menu-title text-truncate">Dashboard</span>
    </a>
</li>

<!-- ===================================================== -->
<!-- DEDUCTION MANAGEMENT (all approved users) -->
<!-- ===================================================== -->
<li class="navigation-header">
    <span>DEDUCTION MANAGEMENT</span>
    <i data-feather="more-horizontal"></i>
</li>

<!-- All Deductions (search, filter, export, print) -->
<li class="{{ Request::is('deductions') || (Request::is('deductions/*') && ! Request::is('deductions/create')) ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deductions.index') }}">
        <i data-feather="file-text"></i>
        <span class="menu-title text-truncate">All Deductions</span>
    </a>
</li>

<!-- New Deduction -->
<li class="{{ Request::is('deductions/create') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deductions.create') }}">
        <i data-feather="plus-circle"></i>
        <span class="menu-title text-truncate">New Deduction</span>
    </a>
</li>

@if ($isAdmin)

    <!-- Reports -->
    <li class="{{ Request::is('reports*') ? 'active' : '' }} nav-item">
        <a class="d-flex align-items-center" href="{{ route('reports.index') }}">
            <i data-feather="bar-chart-2"></i>
            <span class="menu-title text-truncate">Reports</span>
        </a>
    </li>

    <!-- ===================================================== -->
    <!-- MASTER DATA (admin & superadmin) -->
    <!-- ===================================================== -->
    <li class="navigation-header">
        <span>MASTER DATA</span>
        <i data-feather="more-horizontal"></i>
    </li>

    <li class="nav-item has-sub {{ Request::is('branches*') || Request::is('departments*') || Request::is('job-titles*') || Request::is('deduction-types*') ? 'open' : '' }}">
        <a class="d-flex align-items-center" href="#">
            <i data-feather="database"></i>
            <span class="menu-title text-truncate">Setup Tables</span>
            <span class="badge badge-light-warning rounded-pill ms-auto me-1">4</span>
        </a>
        <ul class="menu-content">
            <li class="{{ Request::is('branches*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('branches.index') }}">
                    <i data-feather="circle"></i>
                    <span class="menu-item text-truncate">Branches</span>
                </a>
            </li>
            <li class="{{ Request::is('departments*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('departments.index') }}">
                    <i data-feather="circle"></i>
                    <span class="menu-item text-truncate">Departments</span>
                </a>
            </li>
            <li class="{{ Request::is('job-titles*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('job-titles.index') }}">
                    <i data-feather="circle"></i>
                    <span class="menu-item text-truncate">Job Titles</span>
                </a>
            </li>
            <li class="{{ Request::is('deduction-types*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('deduction-types.index') }}">
                    <i data-feather="circle"></i>
                    <span class="menu-item text-truncate">Deduction Types</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- ===================================================== -->
    <!-- USER MANAGEMENT (admin: users only, superadmin: everyone) -->
    <!-- ===================================================== -->
    <li class="navigation-header">
        <span>USER MANAGEMENT</span>
        <i data-feather="more-horizontal"></i>
    </li>

    <li class="{{ Request::is('users*') ? 'active' : '' }} nav-item">
        <a class="d-flex align-items-center" href="{{ route('users.index') }}">
            <i data-feather="users"></i>
            <span class="menu-title text-truncate">User Approvals</span>
        </a>
    </li>

@endif

@if ($isSuperAdmin)

    <!-- ===================================================== -->
    <!-- LOGS (superadmin only) -->
    <!-- ===================================================== -->
    <li class="navigation-header">
        <span>LOGS</span>
        <i data-feather="more-horizontal"></i>
    </li>

    <!-- Audit trail of every change -->
    <li class="{{ Request::is('audit-logs*') ? 'active' : '' }} nav-item">
        <a class="d-flex align-items-center" href="{{ route('audit-logs.index') }}">
            <i data-feather="shield"></i>
            <span class="menu-title text-truncate">Audit Trail</span>
        </a>
    </li>

    <!-- Deleted records log -->
    <li class="{{ Request::is('soft-delete-logs*') ? 'active' : '' }} nav-item">
        <a class="d-flex align-items-center" href="{{ route('soft-delete-logs.index') }}">
            <i data-feather="trash-2"></i>
            <span class="menu-title text-truncate">Deletion Log</span>
        </a>
    </li>

@endif