@extends('layouts.main')

@section('title', '儀表板')

@section('css')
    <style>
        .googleCalendar{
            position: relative;
            height: 0;
            width: 100%;
            padding-bottom: 80%;
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
                                                                @if(is_array($sys_announcement->fileNames) > 0)
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
                                                        <h5 class="text-muted m-b-0" style="text-align: center;">{{ \Carbon\Carbon::parse($sys_announcement->created_at)->diffForHumans() }} 發佈</h5>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                {{ $sys_announcements->links() }}
                                    @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h4 class="card-title m-t-10" style="padding-right: 20px">課程公告</h4>
                                </div>

                                @if($announcements->count() > 0)

                                    <ul class="list-style-none">

                                        @foreach($announcements as $key=>$announcement)
                                            <li class="d-flex no-block card-body @if($key != 0) border-top @endif">
                                                @if($announcement->status == 1)  {{--active --}}
                                                <i class="fas fa-bullhorn w-30px m-t-5"></i>
                                                @else  {{--not active--}}
                                                <i class="fa fa-hourglass-end w-30px m-t-5"></i>
                                                @endif

                                                <div>
                                                    <a class="link m-b-0 font-medium p-0" data-toggle="collapse" data-parent="#accordian-4" href="#Toggle-{{ $key }}" aria-expanded="false" aria-controls="Toggle-{{ $key }}">
                                                        {{ $announcement->title }}
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
                                                        <h5 class="text-muted m-b-0" style="text-align: center;">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }} 發佈</h5>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    {{ $announcements->links() }}

                                @else
                                    <ul class="list-style-none">
                                        <li class="d-flex no-block card-body">
                                            暫無公告
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>

                        <div class="col-md-6">

                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title m-b-0">待繳交的作業</h5>
                                </div>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">作業名稱</th>
                                        <th scope="col">狀態</th>
                                        <th scope="col">動作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($assignmentCounter = 0)
                                    @foreach($courses as $course)
                                        @foreach($course->assignment as $assignment)
                                            @if($assignment->hide == 0 and ($assignment->student->status == 1 ||$assignment->student->status == 4 ||$assignment->student->status == 6))
                                                @php($assignmentCounter += 1)
                                                <tr>
                                                    <td>{{ $assignment->name }}</td>
                                                    <td class="text-success">
                                                    @switch($assignment->student->status)
                                                        @case(1) <!-- 未繳交 -->
                                                            <span class="badge badge-pill badge-danger">
                                                            未繳交
                                                        </span>
                                                        @break
                                                        @case(4) <!-- 未補繳 -->
                                                            <span class="badge badge-pill badge-danger">
                                                            補繳中
                                                        </span>
                                                        @break
                                                        @case(6) <!-- 未重繳 -->
                                                            <span class="badge badge-pill badge-danger">
                                                            尚未重繳
                                                        </span>
                                                        @break
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('assignment.handInAssignment', ['course_id' => $course->course_id ,'assignment_id' => $assignment->assignment_id]) }}" data-toggle="tooltip" data-placement="top" title="前往作業">
                                                            <i class="mdi mdi-lead-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach

                                    @if($assignmentCounter == 0)
                                        <tr>
                                            <td>無待繳交作業</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="btn-group">
                                        <h4 class="card-title m-t-10" style="padding-right: 20px">行事曆</h4>
                                    </div>
                                    <div class="googleCalendar">
                                        <iframe src="https://calendar.google.com/calendar/embed?src=fjcubm20180731%40gmail.com&ctz=Asia%2FTaipei" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>                                    </div>
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
