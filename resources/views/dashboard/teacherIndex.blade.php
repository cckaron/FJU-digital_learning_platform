@extends('layouts.main')

@section('title', '儀表板')

@section('css')
    <style>
        .googleCalendar{
            position: relative;
            height: 0;
            width: 100%;
            padding-bottom: 70%;
        }

        .googleCalendar iframe{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
@endsection

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

        @include('layouts.partials.pageBreadCrumb', ['title' => '快速選單'])


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

                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <!-- if has any in progress courses, give hyperlink-->
                        <a href="@if($hasInProgressCourse) {{ route('teacher.correctAssignment') }} @else # @endif">
                            <!-- end if -->
                        <div class="card card-hover">
                            <div class="box bg-cyan text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-view-dashboard"></i></h1>
                                @if($hasInProgressCourse)
                                    <h6 class="text-white">作業批改</h6>
                                @else
                                    <h6 class="text-white">批改系統未開放 (無進行中課程）</h6>
                                @endif
                            </div>
                        </div>
                        </a>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <a href="{{ route('announcement.create') }}">
                        <div class="card card-hover">
                            <div class="box bg-success text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-chart-areaspline"></i></h1>
                                <h6 class="text-white">發布公告</h6>
                            </div>
                        </div>
                        </a>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    {{--<div class="col-md-6 col-lg-2 col-xlg-3">--}}
                        {{--<a href="{{ route('courses.showCourses_Teacher') }}">--}}
                        {{--<div class="card card-hover">--}}
                            {{--<div class="box bg-warning text-center">--}}
                                {{--<h1 class="font-light text-white"><i class="mdi mdi-collage"></i></h1>--}}
                                {{--<h6 class="text-white">課程管理</h6>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <a href="{{ route('grade.showlist') }}">
                        <div class="card card-hover">
                            <div class="box bg-danger text-center">
                                <h1 class="font-light text-white"><i class="mdi mdi-border-outside"></i></h1>
                                <h6 class="text-white">成績</h6>
                            </div>
                        </div>
                        </a>
                    </div>
                    <!-- Column -->
                    <div class="col-md-6 col-lg-2 col-xlg-3">
                        <a href="{{ route('teacher.getStudents') }}">
                            <div class="card card-hover">
                                <div class="box bg-info text-center">
                                    <h1 class="font-light text-white"><i class="mdi mdi-arrow-all"></i></h1>
                                    <h6 class="text-white">學生通訊錄</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Column -->

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="btn-group">
                                        <h4 class="card-title m-t-10" style="padding-right: 20px">系統公告</h4>
                                    </div>

                                    @if($sys_announcements->count() > 0)
                                        <ul class="list-style-none">
                                            @foreach($sys_announcements as $key=>$sys_announcement)
                                                <li class="d-flex no-block card-body @if($key != 0) border-top @endif">
                                                    @if($sys_announcement->status == 1)  {{--active --}}
                                                    <i class="fas fa-bullhorn w-30px m-t-5"></i>
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
                                                                    <p class="border border-primary">
                                                                <span class="p-r-5 p-t-5">
                                                                    <i class="fas fa-link m-r-10 m-t-5"></i>附件下載:
                                                                </span>
                                                                        @if(count($sys_announcement->fileNames) > 0)
                                                                            @foreach($sys_announcement->fileNames as $key => $fileName)
                                                                                <a href="{{ route('announcement.attachment.download', ['id' => $sys_announcement->id, 'fileName' => $fileName]) }}" style="color: blue">
                                                                                    <span>{{ $key+1 }}.</span>{{ $fileName }}
                                                                                </a>
                                                                            @endforeach
                                                                        @else
                                                                            無
                                                                        @endif
                                                                    </p>
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

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h4 class="card-title m-t-10" style="padding-right: 20px">行事曆</h4>
                                </div>
                                <div class="googleCalendar">
                                    <iframe src="https://calendar.google.com/calendar/embed?src=mm.fju.edu.tw_b6uth22v4mqalp3qag8v81oulk%40group.calendar.google.com&ctz=Asia%2FTaipei" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
                                </div>
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
