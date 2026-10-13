
<li class="{{ Request::is('dashboard*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
        <i data-feather="home"></i>
        <span class="menu-title text-truncate">
            Dashboard
        </span>
    </a>
</li>

<!-- ===================================================== -->
<!-- INVOICE MANAGEMENT -->
<!-- ===================================================== -->

<li class="navigation-header">
    <span>INVOICE MANAGEMENT</span>
    <i data-feather="more-horizontal"></i>
</li>

<!-- Invoice Reports -->
<li class="{{ Request::is('invoice-reports*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center"
       href="{{ route('invoice-reports.index') }}">
        <i data-feather="file-text"></i>
        <span class="menu-title text-truncate">
            Invoice Reports
        </span>
    </a>
</li>

<!-- Online Transactions -->
<li class="{{ Request::is('online-transactions*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center"
       href="{{ route('online-transactions.index') }}">
        <i data-feather="credit-card"></i>
        <span class="menu-title text-truncate">
            Online Transactions
        </span>
    </a>
</li>

<!-- Awaiting Reports -->
 
<li class="{{ Request::is('awaiting-reports*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center"
       href="{{ route('awaiting-reports.index') }}">
        <i data-feather="clock"></i>
        <span class="menu-title text-truncate">
            Awaiting Reports
        </span>
    </a>
</li>


<!-- Search Existing Invoice -->
<li class="{{ Request::is('invoice-search*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center"
       href="{{ route('invoice-search.index') }}">
        <i data-feather="search"></i>
        <span class="menu-title text-truncate">
            Search Existing Invoice
        </span>
    </a>
</li>

<!-- Deleted records log -->
<li class="{{ Request::is('deletion-log*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{{ route('deletion-log.index') }}">
        <i data-feather="trash-2"></i>
        <span class="menu-title text-truncate">Deletion Log</span>
    </a>
</li>


<!-- ===================================================== -->
<!-- USER MANAGEMENT -->
<!-- ===================================================== -->

@if(auth()->user()->role === 'superadmin' || auth()->user()->role === 'supersuperadmin')

<li class="navigation-header">
    <span>USER MANAGEMENT</span>
    <i data-feather="more-horizontal"></i>
</li>

<li class="{{ Request::is('superadmin/users*') || Request::is('supersuperadmin/users*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center"
       href="{{ auth()->user()->role === 'supersuperadmin' 
            ? route('supersuperadmin.users.index') 
            : route('superadmin.users.index') }}">
        <i data-feather="users"></i>
        <span class="menu-title text-truncate">
            User Approvals
        </span>
    </a>
</li>

@endif

@can('site_contents')
<li class="{{ Request::is('admin/site_contents*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{!! route('site_contents.index') !!}"><i data-feather="layers"></i><span class="menu-title text-truncate" data-i18n="CMS">{{__('lang.admin_menu_cms')}}</span></a>
</li>
@endcan

@canany(['languages','keywords'])
<!-- Apps and Pages -->
<li class="navigation-header"><span data-i18n="Apps &amp; Pages">{{__('lang.admin_menu_localization')}} &amp; {{__('lang.admin_menu_pages')}}</span><i data-feather="more-horizontal"></i></li>

<li class=" nav-item has-sub {{ Request::is('admin/languages*') || Request::is('admin/keywords*') ? 'open':'' }}"
>
    <a class="d-flex align-items-center" href="#"><i data-feather="type"></i><span class="menu-title text-truncate" data-i18n="Localisation">{{__('lang.admin_menu_localisation')}}</span><span class="badge badge-light-warning rounded-pill ms-auto me-1">2</span></a>
    <ul class="menu-content">
        @can('languages')
        <li class="{{ Request::is('admin/languages*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{!! url('admin/languages') !!}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="eCommerce">{{__('lang.admin_menu_language_list')}}</span></a>
        </li>
        @endcan
        @can('keywords')
        <li class="{{ Request::is('admin/keywords*') ? 'active' : '' }}">
            <a class="d-flex align-items-center" href="{!! url('admin/keywords') !!}?lang_code=en"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="eCommerce">{{__('lang.admin_menu_language_keyword')}}</span></a>
        </li>
        @endcan
    </ul>
</li>
@endcanany

@can('notifications')
<li class="{{ Request::is('admin/notifications*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{!! url('admin/notifications') !!}"><i data-feather="bell"></i><span class="menu-title text-truncate" data-i18n="Push Notification">{{__('lang.admin_menu_push_notification')}}</span></a>
</li>
@endcan

<!-- check update -->
@can('check-update')
<li class="{{ Request::is('admin/check-update*') ? 'active' : '' }} nav-item">
    <a class="d-flex align-items-center" href="{!! url('admin/check-update') !!}"><i data-feather="download"></i><span class="menu-title text-truncate" data-i18n="Push Notification">{{__('lang.admin_check_update_option')}}</span>
    @if(setting('website_updates')==true) <span class="badge badge-light-warning rounded-pill ms-auto me-1">1</span> @endif
    </a>
</li>
@endcan

