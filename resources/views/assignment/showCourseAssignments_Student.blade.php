@extends('layouts.main')
@section('title', '課程作業')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />
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

            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('Assignment.createAssignment') }}" method="post">

                    <!-- editor -->
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
                                    <h4 class="card-title">進行中的作業 </h4>
                                </div>
                                <div class="comment-widgets scrollable">

                                    <!-- Assignment Loop Start -->
                                    @php($assignmentCounter = 0)
                                @foreach($assignments_processing as $assignment)
                                    @if($assignment->hide == 0)
                                        @php($assignmentCounter += 1)
                                    <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/homework.png') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h4 class="font-medium">
                                                    {{ $assignment->year }} 年 第 {{ $assignment->semester }} 學期
                                                    <span class="text-muted float-right">截止日期：{{ $assignment->end_date }}</span>
                                                </h4>
                                                <span class="badge badge-pill badge-success float-right"  style="font-size: 100%; margin-right: 10px; margin-top: 5px">
                                                    {{ $assignment->common_course_name }}
                                                </span>
                                                <h4><span class="m-b-15 d-block" style="margin-top: 10px;">{{ $assignment->assignment_name }}</span></h4>
                                                <div class="comment-footer">
                                                    <!-- 按鈕 --> <!-- 1:未繳交; 2:已繳交; 3:審核完成; -->
                                                    @if($assignment->assignment_status == 1 or $assignment->assignment_status == 2)
                                                        <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">繳交作業</a>
                                                    @elseif($assignment->assignment_status == 3 or $assignment->assignment_status == 5 or $assignment->assignment_status == 7)
                                                        <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-default btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看詳情</a>
                                                    @elseif($assignment->assignment_status == 4)
                                                        <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">補繳作業</a>
                                                    @elseif($assignment->assignment_status == 6)
                                                        <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">重繳作業</a>
                                                    @endif

                                                <!-- 狀態 -->
                                                @switch($assignment->student_assignment_status)
                                                    @case(1) <!-- 未繳交 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                            未繳交
                                                        </span>
                                                    @break
                                                    @case(2) <!-- 已繳交 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                            已繳交
                                                        </span>
                                                    @break
                                                    @case(3) <!-- 教師已批改 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                            教師已批改
                                                        </span>
                                                    @break
                                                    @case(4) <!-- 未補繳 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                            補繳中
                                                        </span>
                                                    @break
                                                    @case(5) <!-- 已補繳 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                            已補繳作業
                                                        </span>
                                                    @break
                                                    @case(6) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                            尚未重繳
                                                        </span>
                                                    @break
                                                    @case(7) <!-- 已重繳 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                            已重繳作業
                                                        </span>
                                                    @break
                                                @endswitch

                                                <!-- 成績 -->
                                                    <span class="badge badge-pill badge-secondary float-right" style="font-size: 100%; margin-right: 10px; margin-top: 5px">
                                                       @if($assignment->announce_score == 1)
                                                            @if($assignment->score == null)
                                                                成績：未評分
                                                            @else
                                                                成績：{{ round($assignment->score, 2) }}
                                                            @endif
                                                        @else
                                                            成績：不公布
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @endforeach

                                    @if($assignmentCounter == 0)
                                        <div class="m-l-30 m-b-10">
                                            無進行中的作業
                                        </div>
                                    @endif
                                </div>
                            </div>



                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">已結束的作業 </h4>
                                </div>
                                <div class="comment-widgets scrollable">

                                    <!-- Assignment Loop Start -->
                                @php($assignmentCounter = 0)
                                @foreach($assignments_finished as $assignment)
                                    @if($assignment->hide == 0)
                                        @php($assignmentCounter += 1)
                                        <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/homework.png') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h4 class="font-medium">
                                                    {{ $assignment->year }} 年 第 {{ $assignment->semester }} 學期
                                                    <span class="text-muted float-right">截止日期：{{ $assignment->end_date }}</span>
                                                </h4>
                                                <span class="badge badge-pill badge-success float-right"  style="font-size: 100%; margin-right: 10px; margin-top: 5px">
                                                    {{ $assignment->common_course_name }}
                                                </span>
                                                <h4><span class="m-b-15 d-block" style="margin-top: 10px;">{{ $assignment->assignment_name }}</span></h4>
                                                <div class="comment-footer">
                                                    <!-- 按鈕 --> <!-- 1:未繳交; 2:已繳交; 3:審核完成; -->
                                                    @switch($assignment->student_assignment_status)
                                                        @case(1)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">繳交作業</a>
                                                        @break
                                                        @case(2)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">繳交作業</a>
                                                        @break
                                                        @case(3)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-default btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看詳情</a>
                                                        @break
                                                        @case(4)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">補繳作業</a>
                                                        @break
                                                        @case(5)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-default btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看詳情</a>
                                                        @break
                                                        @case(6)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">重繳作業</a>
                                                        @break
                                                        @case(7)
                                                            <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignment->course_id ,'assignment_id' => $assignment->assignment_id]) }}" class="btn btn-default btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看詳情</a>
                                                        @break
                                                    @endswitch

                                                <!-- 狀態 -->
                                                @switch($assignment->student_assignment_status)
                                                    @case(1) <!-- 未繳交 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                        未繳交
                                                    </span>
                                                    @break
                                                    @case(2) <!-- 已繳交 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                        已繳交
                                                    </span>
                                                    @break
                                                    @case(3) <!-- 教師已批改 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                        教師已批改
                                                    </span>
                                                    @break
                                                    @case(4) <!-- 未補繳 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                        未補繳
                                                    </span>
                                                    @break
                                                    @case(5) <!-- 已補繳 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                        已補繳 (尚未批改)
                                                    </span>
                                                    @break
                                                    @case(6) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-danger float-right" style="font-size: 100%; margin-top: 5px">
                                                        未重繳
                                                    </span>
                                                    @break
                                                    @case(7) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-primary float-right" style="font-size: 100%; margin-top: 5px">
                                                            已重繳 (尚未批改)
                                                        </span>
                                                    @break
                                                @endswitch

                                                <!-- 成績 -->
                                                    <span class="badge badge-pill badge-secondary float-right" style="font-size: 100%; margin-right: 10px; margin-top: 5px">
                                                        @if($assignment->announce_score == 1)
                                                            @if($assignment->score == null)
                                                                成績：未評分
                                                            @else
                                                                成績：{{ round($assignment->score, 2) }}
                                                            @endif
                                                        @else
                                                            成績：不公布
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @endforeach

                                    @if($assignmentCounter == 0)
                                        <div class="m-l-30 m-b-10">
                                            無已結束的作業
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                    {{ csrf_field() }}
                </form>

                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                All Rights Reserved by Chun-Kai Kao. Technical problem please contact: cckaron28@gmail.com
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

@endsection

@section('scripts')
    <script src="{{ URL::to('libs/inputmask/dist/min/jquery.inputmask.bundle.min.js') }}"></script>
    <script src="{{ URL::to('js/pages/mask/mask.init.js') }}"></script>
    <script src="{{ URL::to('libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ URL::to('libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ URL::to('libs/jquery-asColor/dist/jquery-asColor.min.js') }}"></script>
    <script src="{{ URL::to('libs/jquery-asGradient/dist/jquery-asGradient.js') }}"></script>
    <script src="{{ URL::to('libs/jquery-asColorPicker/dist/jquery-asColorPicker.min.js') }}"></script>
    <script src="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.min.js') }}"></script>
    <script src="{{ URL::to('libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/quill.min.js') }}"></script>

    <script>
        //***********************************//
        // For select 2
        //***********************************//
        $(".select2").select2();

        /*colorpicker*/
        $('.demo').each(function() {
            //
            // Dear reader, it's actually very easy to initialize MiniColors. For example:
            //
            //  $(selector).minicolors();
            //
            // The way I've done it below is just for the demo, so don't get confused
            // by it. Also, data- attributes aren't supported at this time...they're
            // only used for this demo.
            //
            $(this).minicolors({
                control: $(this).attr('data-control') || 'hue',
                position: $(this).attr('data-position') || 'bottom left',

                change: function(value, opacity) {
                    if (!value) return;
                    if (opacity) value += ', ' + opacity;
                    if (typeof console === 'object') {
                        console.log(value);
                    }
                },
                theme: 'bootstrap'
            });

        });
        /*datwpicker*/
        jQuery('.mydatepicker').datepicker();
        jQuery('#datepicker-start').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        jQuery('#datepicker-end').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        var quill = new Quill('#editor', {
            theme: 'snow'
        });

    </script>




@endsection
