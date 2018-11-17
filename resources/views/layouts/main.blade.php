<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::to('images/favicon.png') }}">
    <title>@yield('title')</title>
    <!-- Custom CSS -->

    <link href="{{ URL::to('libs/fullcalendar/dist/fullcalendar.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('extra-libs/calendar/calendar.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('extra-libs/multicheck/multicheck.css') }} " rel="stylesheet"/>
    <link href="{{ URL::to('libs/datatables.net-bs4/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />

    @yield('css')
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

@yield('content')

<!-- All Jquery -->
<!-- ============================================================== -->
<script src="{{ URL::to('libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ URL::to('js/jquery.ui.touch-punch-improved.js') }}"></script>
<script src="{{ URL::to('js/jquery-ui.min.js') }}"></script>
<!-- Bootstrap tether Core JavaScript -->
<script src="{{ URL::to('libs/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ URL::to('libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- slimscrollbar scrollbar JavaScript -->
<script src="{{ URL::to('libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
<script src="{{ URL::to('extra-libs/sparkline/sparkline.js') }}"></script>
<!--Wave Effects -->
<script src="{{ URL::to('js/waves.js') }}"></script>
<!--Menu sidebar -->
<script src="{{ URL::to('js/sidebarmenu.js') }}"></script>
<!--Custom JavaScript -->
<script src="{{ URL::to('js/custom.min.js') }}"></script>

<!--Datatable -->
<script src="{{ URL::to('extra-libs/multicheck/datatable-checkbox-init.js') }}"></script>
<script src="{{ URL::to('extra-libs/multicheck/jquery.multicheck.js') }}"></script>
<script src="{{ URL::to('extra-libs/DataTables/datatables.min.js') }}"></script>

@yield('scripts')
</body>

</html>