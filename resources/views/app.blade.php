<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Prevent the demo from appearing in search engines -->
    <meta name="robots" content="noindex">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700%7CRoboto:400,500%7CExo+2:600&display=swap" rel="stylesheet">
    <!-- Preloader -->
    <link type="text/css" href="/vendor/spinkit.css" rel="stylesheet">
    <!-- Perfect Scrollbar -->
    <link type="text/css" href="/vendor/perfect-scrollbar.css" rel="stylesheet">
    <!-- Material Design Icons -->
    <link type="text/css" href="/css/material-icons.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link type="text/css" href="/css/fontawesome.css" rel="stylesheet">
    <!-- Preloader -->
    <link type="text/css" href="/css/preloader.css" rel="stylesheet">
    <!-- Dropzone -->
    <link type="text/css" href="/css/dropzone.css" rel="stylesheet">
    <script src="{{ url('/vendor/jquery.min.js') }}"></script>
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <script src="{{ mix('/js/app.js') }}" defer></script>
    <script src="{{ mix('/js/manifest.js') }}" defer></script>
    <script src="{{ mix('/js/vendor.js') }}" defer></script>
    @inertiaHead
</head>
<body class="layout-app">
<div class="preloader">
    <div class="sk-chase">
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
        <div class="sk-chase-dot"></div>
    </div>
</div>
<!-- Drawer Layout -->
@inertia
<!-- // END Drawer Layout -->

<!-- Bootstrap -->
<script src="{{ url('/vendor/popper.min.js') }}"></script>
<script src="{{ url('/vendor/bootstrap.min.js') }}"></script>
<script src="{{ url('/vendor/perfect-scrollbar.min.js') }}"></script>
<!-- DOM Factory -->
<script src="{{ url('/vendor/dom-factory.js') }}"></script>
<!-- MDK -->
<script src="{{ url('/vendor/material-design-kit.js') }}"></script>
{{--<script src="{{ url('/js/app.js') }}"></script>--}}

<script src="/js/preloader.js"></script>
<script>
    jQuery(document).ready(function(){
        jQuery('#navBtn').on('click',function (){
            jQuery("#default-drawer").attr('data-opened','');
            jQuery(".layout-app").addClass('has-drawer-opened');
        })
    })
</script>
</body>
</html>

