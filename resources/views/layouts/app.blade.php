<!DOCTYPE html>
<html class="loading hide bordered-layout" lang="en" data-layout="bordered-layout" data-textdirection="ltr"><!-- dark-layout -->
<head>
    @PwaHead
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Mobile APP">
    <meta name="keywords" content="Mobile APP">
    <meta name="author" content="Team June">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{('Salary deduction - Admin')}}</title>
    @if(('website_favicon')!='')
    <link rel="icon" type="image/x-icon" href="{{url('site-assets/images/website')}}" />
    @else
    <link rel="icon" type="image/x-icon" href="{{url('uploads/no-favicon.ico')}}" />
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/toastr.min.css')}}">
    <!-- Datatable css -->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    @php
        $direct = "";
        if (Session()->get('admin_direction') != '') {
            if (Session()->get('admin_direction') == 'rtl') {
                $direct = "-rtl";
            }
        }
    @endphp
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css' . $direct . '/bootstrap.css') }}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/themes/bordered-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/themes/semi-dark-layout.css')}}">

    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/core/menu/menu-types/vertical-menu.css')}}">
    
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/plugins/extensions/ext-component-toastr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css' . $direct . '/plugins/forms/pickers/form-flat-pickr.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style' . $direct . '.css')}}">
     <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/app-todo.css')}}">

     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script>
        var base_url = '{{url("")}}';
    </script>
    <style type="text/css">
        .pac-container{
                z-index: 10010;
        }
        /* Hide DataTables' default sort icons */
        table.dataTable thead > tr > th.sorting:before,
        table.dataTable thead > tr > th.sorting:after,
        table.dataTable thead > tr > th.sorting_asc:before,
        table.dataTable thead > tr > th.sorting_asc:after,
        table.dataTable thead > tr > th.sorting_desc:before,
        table.dataTable thead > tr > th.sorting_desc:after,
        table.dataTable thead > tr > th.sorting_asc_disabled:before,
        table.dataTable thead > tr > th.sorting_asc_disabled:after,
        table.dataTable thead > tr > th.sorting_desc_disabled:before,
        table.dataTable thead > tr > th.sorting_desc_disabled:after {
            content: "" !important;
        }
    </style>
</head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
@stack('scripts')

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
    <nav class="header-navbar navbar navbar-expand-lg align-items-center navbar-light navbar-shadow container-xxl fixed-top">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item dropdown dropdown-language">
                    <?php
                    

                    if (Session()->has('admin_locale') AND array_key_exists(Session()->get('admin_locale'), config('languages'))) {
                        $langCode = Session()->get('admin_locale');
                    }
                    else { // This is optional as Laravel will automatically set the fallback language if there is none specified
                        $langCode = config('app.fallback_locale');
                    }
                    ?>
                    
                    
                </li>
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style">
                        <i class="ficon" data-feather="sun" id="dark"></i>
                        <i class="ficon" data-feather="moon" id="light"></i>
                    </a>
                </li>
                <li class="nav-item dropdown dropdown-user">
                    @auth
    <a class="nav-link dropdown-toggle" id="dropdown-user"
       href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

        <span class="fw-semibold text-dark">
            {{ auth()->user()->name }}
        </span>

    </a>
    @endauth

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="#">
                            <i class="me-50" data-feather="user"></i> {{__('lang.admin_profile')}}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{!! route('logout') !!}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="me-50" data-feather="power"></i> {{__('lang.admin_logout')}}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="form-horizontal" style="display: none;">{{ csrf_field() }}</form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item me-auto">
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
    <h2 class="fw-bold mb-0" style="
        letter-spacing: 1px;
        color: #14a1a7;
        font-size: 1.6rem;
    ">
        Deduction
    </h2>
</a>

                </li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                @include('layouts.menu')
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    @yield('content')
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">{{__('lang.admin_footer_copyright')}} &copy; <?php echo date("Y");?>, {{__('lang.admin_footer_text')}}</span></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->
    @if(Session::has('message'))
    <script>
        setTimeout(function () {
            toastr['success']('You have successfully logged in. Now you can start to explore!','👋 Welcome Admin!',{
                closeButton: true,
                tapToDismiss: false,
                rtl: isRtl
            });
        }, 2000);
    </script>
    @endif

    <!-- BEGIN: Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN Vendor JS-->

    
    <script src="{{asset('app-assets/vendors/js/extensions/toastr.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>

    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{asset('app-assets/js/scripts/components/components-tooltips.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/pages/app-user-edit.js')}}"></script>
    <script src="{{asset('assets/js/scripts.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/forms/pickers/form-pickers.js')}}"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            function disablePrev() { window.history.forward() }
            disablePrev();
            window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
            $("html").removeClass('hide');
            if(localStorage.getItem("bordered-layout-current-skin")=='dark-layout'){
                $("html").addClass("dark-layout");
                $("html").removeClass("bordered-layout");
                $("#light").remove();
            }else{
                $("html").addClass("bordered-layout");
                $("html").removeClass("dark-layout");
                $("#dark").remove();
            }
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
            @if(Session::has('error'))
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.error("{{ session('error') }}");
            @endif

            @if(Session::has('info'))
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.info("{{ session('info') }}");
            @endif

            @if(Session::has('warning'))
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.warning("{{ session('warning') }}");
            @endif

            @if(Session::has('success'))
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.success("{{ session('success') }}");
            @endif
        })
    </script>

    <?php if (Request::is('admin/settings/app/websetting*')){ ?>

        <script type="text/javascript">
            $('#website_logo').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.website_logo').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });

            $('#about_product').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('#about_product_src').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });

            $('#mobile_image').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('#mobile_image_img').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });


            $('#icon_1').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.icon_1').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });

            $('#icon_2').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.icon_2').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });

            $('#icon_3').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.icon_3').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });

            $('#icon_4').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.icon_4').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });
            $('#website_bg_image').on('change', function (e) {
                var reader = new FileReader(),
                files = e.target.files;
                reader.onload = function () {
                    $('.website_bg_image').attr('src', reader.result);
                };
                reader.readAsDataURL(files[0]);
            });
    </script>
    <?php } ?>

    <!-- BEGIN: Content-->
   @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script type="text/javascript">
                toastr.options =
                {
                    "closeButton" : true,
                    "progressBar" : true
                }
                toastr.error("{{ $error }}");
            </script>
        @endforeach
    @endif


    <!-- custom js code -->
    <style type="text/css">
    .form-check.me-3.me-lg-5.permission {
    cursor: pointer;
    font-weight: 600;
    }
    input.form-check-input {
    cursor: pointer;
    font-weight: 800;
    }

    label.form-label {
        float: left;
    }

    label .required {
    color: red!important;
    }


    .main-menu.menu-dark .navigation > li.active > a {
        background: linear-gradient(118deg, #14a1a7, #14a1a77a) !important;
        box-shadow: 0 0 10px 1px #7a340238 !important;
        color: #fff !important;
        font-weight: 400 !important;
        border-radius: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        display: none!important;
    }

    .select2-container--default .select2-selection--single {
        height: 35px!important;
    }

    .form-check-input:checked {
        background-color: #14a1a7!important;
        border-color: #14a1a7!important;
    }

    input#reportrange {
    background: white;
    }

    input#order_delivery_date {
    background: white;
    }

    .swal-confirm-button-class {
        background-color: #14a1a7 !important; /* Set the desired background color */
    }


    /* Global readability improvements */
body {
    font-size: 20px !important;
    font-weight: 800 !important;
}

/* Sidebar */
.navigation li a {
    font-size: 17px !important;
    font-weight: 600 !important;
}

/* Section headers */
h1, h2, h3, h4, h5, h6,
.card-title {
    font-weight: 800 !important;
}

/* Forms */
.form-label,
label {
    font-size: 20px !important;
    font-weight: 800 !important;
}

.form-control,
.form-select {
    font-size: 15px !important;
    font-weight: 500 !important;
    min-height: 42px;
}

/* Placeholder text */
.form-control::placeholder {
    color: #f9fafc !important;
    opacity: 1 !important;
}

/* Tables */
.table th {
    font-size: 20px !important;
    font-weight: 700 !important;
}

.table td {
    font-size: 15px !important;
    font-weight: 500 !important;
}

/* Buttons */
.btn {
    font-size: 15px !important;
    font-weight: 600 !important;

    
}
    </style>

    <script type="text/javascript">
        function selectAllSameData(source_class, des_class) {
            var sourceChecked = $("." + source_class + ":checked").length > 0;

            if (sourceChecked) {
                $("." + des_class).prop("checked", true);
            } else {
                $("." + des_class).prop("checked", false);
            }
        }


        function showImagePreview(input_id,preview_id,width,height) {
            console.log(width);
            console.log(height);
            $('#'+input_id).on('change', function() {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function(event) {
                    var img = new Image();
                    img.onload = function() {
                        $("#"+preview_id).removeClass('hide');
                        $('#'+preview_id).attr('src', event.target.result);             
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            });
        }


        function translateByThirdParty(name,code,input_id){
            var data = {};
            data.code = code;
            data.name = name;
            data.text = $("#"+input_id).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: base_url+'/admin/translation-by-third-party',
                type: 'POST',
                data: data,
                dataType:'json',
                success: function (response) {
                    if(response.status){
                        $("#"+input_id).val(response.data);
                        myToastr(response.message,'success');
                    }else{
                        myToastr(response.message,'error');
                    }
                },
            });
        }


        function translateByGoogle(name,code,input_id){
            var data = {};
            data.code = code;
            data.name = name;
            data.text = $("#"+input_id).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: base_url+'/admin/translation-by-google',
                type: 'POST',
                data: data,
                dataType:'json',
                success: function (response) {
                    if(response.status){
                        $("#"+input_id).val(response.data);
                        myToastr(response.message,'success');
                    }else{
                        myToastr(response.message,'error');
                    }
                },
            });
        }

        function myToastr(msg, type) {
            toastr.remove();
            if (type == 'error') {
                toastr['error']('', msg);
            } else if (type == 'success') {
                toastr['success']('', msg);
            }
        }

        function translationKeyNotFoundError(){
            myToastr("{{__('lang.admin_google_translate_api_key_error')}}",'error');
        }
    </script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    function showDeleteConfirmation(pagename , itemId) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
            customClass: {
                confirmButton: 'swal-confirm-button-class'
            }
        }).then((result) => {
            if (result.isConfirmed) {
               
                if(pagename == 'product'){
                  window.location.href = base_url+"/admin/products-destroy/" + itemId;
                }else if(pagename == 'order-list'){
                  window.location.href = base_url+"/admin/orders-destroy/" + itemId;
                }else if(pagename == 'user'){
                  window.location.href = base_url+"/admin/user-destroy/" + itemId;
                }else if(pagename == 'driver'){
                  window.location.href = base_url+"/admin/driver-destroy/" + itemId;
                }else if(pagename == 'subadmin'){
                  window.location.href = base_url+"/admin/subadmin-destroy/" + itemId;
                }else if(pagename == 'role'){
                  window.location.href = base_url+"/admin/role-destroy/" + itemId;
                }else if(pagename == 'banner'){
                  window.location.href = base_url+"/admin/banner-destroy/" + itemId;
                }else if(pagename == 'coupon'){
                  window.location.href = base_url+"/admin/coupon-destroy/" + itemId;
                }else if(pagename == 'about_us'){
                  window.location.href = base_url+"/admin/about_us-destroy/" + itemId;
                }else if(pagename == 'holidays'){
                  window.location.href = base_url+"/admin/holidays-destroy/" + itemId;
                }else if(pagename == 'delivery_address'){
                  window.location.href = base_url+"/admin/delivery_areas-destroy/" + itemId;
                }else if(pagename == 'timeslot'){
                  window.location.href = base_url+"/admin/timeslot-destroy/" + itemId;
                }else if(pagename == 'currency'){
                  window.location.href = base_url+"/admin/currency-destroy/" + itemId;
                }else if(pagename == 'language'){
                  window.location.href = base_url+"/admin/language-destroy/" + itemId;
                }else if(pagename == 'notification'){
                  window.location.href = base_url+"/admin/notification-destroy/" + itemId;
                }
                
            }
        });
    }
</script>

<script>
(function () {
    'use strict';
 
    // Refresh the CSRF token every 20 minutes.
    // This prevents 419 Page Expired errors when the user
    // has the PWA open on their device for a long time.
    var INTERVAL = 20 * 60 * 1000; // 20 minutes
 
    function refreshToken() {
        fetch('/csrf-token-refresh', {
            method:      'GET',
            credentials: 'same-origin',
            headers:     { 'Accept': 'application/json' }
        })
        .then(function (res) {
            if (!res.ok) return;
            return res.json();
        })
        .then(function (data) {
            if (!data || !data.token) return;
 
            // 1. Update the meta tag (used by fetch() calls)
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', data.token);
 
            // 2. Update all hidden _token fields on the page
            document.querySelectorAll('input[name="_token"]').forEach(function (el) {
                el.value = data.token;
            });
 
            // 3. Update jQuery AJAX default header (theme uses jQuery)
            if (typeof $ !== 'undefined') {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': data.token }
                });
            }
 
            console.log('[CSRF] Token refreshed.');
        })
        .catch(function () {
            // Silent fail — next form submission will get a 419
            // but at least it won't happen during idle time
        });
    }
 
    setInterval(refreshToken, INTERVAL);
})();
</script>    
    @RegisterServiceWorkerScript
</body>
</html>