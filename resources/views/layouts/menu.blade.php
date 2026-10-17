{{-- Dashboard --}}
<li class="{{ Request::is('dashboard*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
        <i data-feather="home"></i>
        <span class="menu-title text-truncate">Dashboard</span>
    </a>
</li>

{{-- ===================================================== --}}
{{-- DEDUCTIONS --}}
{{-- ===================================================== --}}
<li class="navigation-header">
    <span>DEDUCTIONS</span>
    <i data-feather="more-horizontal"></i>
</li>

<li class="{{ Request::is('deductions*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deductions.index') }}">
        <i data-feather="file-text"></i>
        <span class="menu-title text-truncate">All Deductions</span>
    </a>
</li>

<li class="{{ Request::is('deductions/create') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deductions.create') }}">
        <i data-feather="plus-circle"></i>
        <span class="menu-title text-truncate">New Deduction</span>
    </a>
</li>

<li class="{{ Request::is('reports*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('reports.index') }}">
        <i data-feather="bar-chart-2"></i>
        <span class="menu-title text-truncate">Reports</span>
    </a>
</li>

{{-- ===================================================== --}}
{{-- MASTER DATA --}}
{{-- ===================================================== --}}
<li class="navigation-header">
    <span>MASTER DATA</span>
    <i data-feather="more-horizontal"></i>
</li>

<li class="{{ Request::is('branches*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('branches.index') }}">
        <i data-feather="map-pin"></i>
        <span class="menu-title text-truncate">Branches</span>
    </a>
</li>

<li class="{{ Request::is('sections*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('sections.index') }}">
        <i data-feather="grid"></i>
        <span class="menu-title text-truncate">Sections</span>
    </a>
</li>

<li class="{{ Request::is('departments*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('departments.index') }}">
        <i data-feather="briefcase"></i>
        <span class="menu-title text-truncate">Departments</span>
    </a>
</li>

<li class="{{ Request::is('job-titles*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('job-titles.index') }}">
        <i data-feather="award"></i>
        <span class="menu-title text-truncate">Job Titles</span>
    </a>
</li>

<li class="{{ Request::is('deduction-types*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deduction-types.index') }}">
        <i data-feather="tag"></i>
        <span class="menu-title text-truncate">Deduction Types</span>
    </a>
</li>

{{-- ===================================================== --}}
{{-- LOGS (Superadmin only) --}}
{{-- ===================================================== --}}
@if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'supersuperadmin')

<li class="navigation-header">
    <span>LOGS & AUDIT</span>
    <i data-feather="more-horizontal"></i>
</li>

<li class="{{ Request::is('audits*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('audits.index') }}">
        <i data-feather="activity"></i>
        <span class="menu-title text-truncate">Audit Trails</span>
    </a>
</li>

<li class="{{ Request::is('soft-delete-logs*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('soft-delete-logs.index') }}">
        <i data-feather="trash-2"></i>
        <span class="menu-title text-truncate">Soft Delete Logs</span>
    </a>
</li>

{{-- User Management --}}
<li class="navigation-header">
    <span>USER MANAGEMENT</span>
    <i data-feather="more-horizontal"></i>
</li>

<li class="{{ Request::is('superadmin/users*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('superadmin.users.index') }}">
        <i data-feather="users"></i>
        <span class="menu-title text-truncate">User Approvals</span>
    </a>
</li>

@endif