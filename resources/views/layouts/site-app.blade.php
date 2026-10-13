<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="{{setting('site_description')}}">
        <meta name="keywords" content="{{setting('site_keyword')}}">
        <meta name="author" content="{{setting('site_author')}}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
        <title>{{setting('site_title')}}</title>
        @if(setting('website_favicon')!='')
        <link rel="icon" type="image/x-icon" href="{{url('site-assets/images/website/'.setting('website_favicon'))}}" />
        @else
        <link rel="icon" type="image/x-icon" href="{{url('uploads/no-favicon.ico')}}" />
        @endif
        <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/font-awesome.css')}}"/>
        <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/bootstrap.css')}}"/>
        <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/owl.theme.default.min.css')}}" />
        <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/owl.carousel.min.css')}}" />
        @if(isset($_COOKIE['lang_code']))
            @if($_COOKIE['lang_code']=='ar')
                <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/style-rtl.css')}}"/>
            @else
                <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/style.css')}}"/>
            @endif
        @else
            <link rel="stylesheet" type="text/css" href="{{asset('site-assets/css/style.css')}}"/>
        @endif
        <link rel="stylesheet" href="{{asset('site-assets/aicon/style.css')}}" />
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUNZZgE72aRG8H_aW_f9K1y1SedPg3LJI&libraries=places"></script>
        <script>
            function disablePrev() { window.history.forward() }
            window.onload = disablePrev();
            window.onpageshow = function(evt) { if (evt.persisted) disableBack() }
            var base_url = "<?php echo url(''); ?>";
        </script>
        <script src="{{asset('site-assets/js/jquery-3.3.1.js')}}"></script>
        <?php if (Request::is('cart*')) { ?>
            <script src="{{asset('site-assets/js/mobiscroll.jquery.min.js')}}"></script>
            <link rel="stylesheet" href="{{asset('site-assets/css/mobiscroll.jquery.min.css')}}" />
        <?php } ?>
        <?php echo setting('google_analysis'); ?>
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "icdllf1rz7");
        </script>
        <script src="https://www.youtube.com/iframe_api"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <?php echo setting('google_analytics'); ?>
    </head>
    <style type="text/css">
        /*.headerbg{
            background: url("{{asset('site-assets/images/website/')}}/{{setting('website_bg_image')}}") repeat;
        }*/
    </style>
       <body>
        <!-- BEGIN: Content-->
        @yield('sitecontent')
        <!-- END: Content-->
    </body>
</html>