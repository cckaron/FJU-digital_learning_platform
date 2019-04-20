@extends('layouts.main')

@section('title', '儀表板')

@section('content')

    @include('layouts.partials.preloader')

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">

    @include('layouts.partials.header')

    @include('layouts.partials.leftSidebar')

    <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">

        {{--@include('layouts.partials.pageBreadCrumb', ['title' => '儀表板'])--}}


        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Sales Cards  -->
                <!-- ============================================================== -->
                <div class="row">
                    @if(session()->has('message'))
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">提示</h5>
                                    <div class="alert alert-success" role="alert">
                                        {{ session()->get('message') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h4 class="card-title m-t-10" style="padding-right: 20px">系統公告</h4>
                                </div>

                                @if(count($sys_announcements) > 0)
                                <ul class="list-style-none">
                                    @foreach($sys_announcements as $key=>$sys_announcement)
                                        <li class="d-flex no-block card-body @if($key != 0) border-top @endif">
                                            @if($sys_announcement->status == 1)  {{--active --}}
                                            <i class="fa fa-check-circle w-30px m-t-5"></i>
                                            @else  {{--not active--}}
                                            <i class="fa fa-hourglass-end w-30px m-t-5"></i>
                                            @endif

                                            <div>
                                                <a class="link m-b-0 font-medium p-0" data-toggle="collapse" data-parent="#accordian-4" href="#Toggle-sys-{{ $key }}" aria-expanded="false" aria-controls="Toggle-{{ $key }}">
                                                    {{ $sys_announcement->title }}

                                                    {{--<span class="text-active p-l-5" >--}}
                                                        {{--@if($announcement->status == 1)--}}
                                                            {{--<span class="badge badge-pill badge-primary">已發布</span>--}}
                                                        {{--@else--}}
                                                            {{--<span class="badge badge-pill badge-dark">未發佈</span>--}}
                                                        {{--@endif--}}
                                                        {{--</span>--}}

                                                    <span class="text-active p-l-5" >
                                                            @if($sys_announcement->priority == 0)
                                                            <span class="badge badge-pill badge-danger">置頂公告</span>
                                                        @elseif($sys_announcement->priority == 1)
                                                            <span class="badge badge-pill badge-primary">一般</span>
                                                        @endif
                                                        </span>
                                                </a>

                                                <div class="p-t-5">

                                                    <div id="Toggle-sys-{{ $key }}" class="multi-collapse collapse p-t-10" style="">
                                                        <div class="widget-content">
                                                            <h6>
                                                                {!! $sys_announcement->content !!}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-auto">
                                                <div class="text-right">
                                                    <div class="p-t-5">
                                                        <h5 class="text-muted m-b-0" style="text-align: center;">{{ \Carbon\Carbon::parse($sys_announcement->created_at)->diffForHumans() }} 發佈</h5>

                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    @endforeach
                                </ul>
                                {{ $sys_announcements->links() }}

                                    @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6"></div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h4 class="card-title m-t-10" style="padding-right: 20px">課程公告</h4>
                                </div>

                                @if(count($announcements) > 0)

                                <ul class="list-style-none">

                                    @foreach($announcements as $key=>$announcement)
                                        <li class="d-flex no-block card-body @if($key != 0) border-top @endif">
                                            @if($announcement->status == 1)  {{--active --}}
                                            <i class="fa fa-check-circle w-30px m-t-5"></i>
                                            @else  {{--not active--}}
                                            <i class="fa fa-hourglass-end w-30px m-t-5"></i>
                                            @endif

                                            <div>
                                                <a class="link m-b-0 font-medium p-0" data-toggle="collapse" data-parent="#accordian-4" href="#Toggle-{{ $key }}" aria-expanded="false" aria-controls="Toggle-{{ $key }}">
                                                    {{ $announcement->title }}

                                                    {{--<span class="text-active p-l-5" >--}}
                                                    {{--@if($announcement->status == 1)--}}
                                                    {{--<span class="badge badge-pill badge-primary">已發布</span>--}}
                                                    {{--@else--}}
                                                    {{--<span class="badge badge-pill badge-dark">未發佈</span>--}}
                                                    {{--@endif--}}
                                                    {{--</span>--}}

                                                    <span class="text-active p-l-5" >
                                                            @if($announcement->priority == 0)
                                                            <span class="badge badge-pill badge-danger">置頂公告</span>
                                                        @elseif($announcement->priority == 1)
                                                            <span class="badge badge-pill badge-primary">一般</span>
                                                        @endif
                                                        </span>
                                                </a>

                                                <div class="p-t-5">

                                                    <div id="Toggle-{{ $key }}" class="multi-collapse collapse p-t-10" style="">
                                                        <div class="widget-content">
                                                            <h6>
                                                                {!! $announcement->content !!}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="ml-auto">
                                                <div class="text-right">
                                                    <div class="p-t-5">
                                                        {{--<strong><span class="text-muted font-16">@if($course->semester == 1)上@else下@endif學期</span></strong>--}}
                                                        <h5 class="text-muted m-b-0" style="text-align: center;">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }} 發佈</h5>

                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    @endforeach
                                </ul>
                                {{ $announcements->links() }}

                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                Designed and Developed by Chun-Kai-Kao. Bug Reports: cg.workst@gmail.com
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->

@endsection

@section('scripts')
    <script src="{{ URL::to('libs/moment/min/moment.min.js') }}"></script>
    <script src="{{ URL::to('libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script src="{{ URL::to('js/pages/calendar/cal-init.js') }}"></script>
@endsection
