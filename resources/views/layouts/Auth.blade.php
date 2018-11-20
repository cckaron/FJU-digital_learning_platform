<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::to('images/favicon.png') }}">
    <title>@yield('title')</title>
    <!-- Custom CSS -->
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    @yield('styles')
</head>
<body>

@yield('content')

<!-- ============================================================== -->
<!-- All Required js -->
<!-- ============================================================== -->
<script type="text/javascript" src="{{ URL::to('libs/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script type="text/javascript" src="{{ URL::to('libs/popper.js/dist/umd/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!--[if lt IE 9] -->
<script type="text/javascript" src="{{ URL::to('js/html5shiv.js') }}"></script>
<script type="text/javascript" src="{{ URL::to('js/respond.min.js') }}"></script>
<!--[endif]-->

@yield('scripts')
</body>
</html>
