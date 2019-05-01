@extends('layouts.main')
@section('title', '我的課程')

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
                                    <h4 class="card-title">進行中的課程 </h4>
                                </div>
                                <div class="comment-widgets scrollable">

                                    <!-- Course Loop Start -->
                                @foreach($student_courses_processing as $student_course)

                                    <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/course.png') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h4 class="font-medium">
                                                    {{ $student_course->year }} 年 第 {{ $student_course->semester }} 學期
                                                    <span class="text-muted float-right">結束日期：{{ $student_course->end_date }}</span>
                                                </h4>

                                                <span class="badge badge-pill badge-info float-right"  style="font-size: 100%;">
                                                    {{ $student_course->common_course_name }}
                                                </span>

                                                <h4>
                                                    <span class="m-b-15 d-block" style="margin-top: 10px;">{{ $student_course->name }}</span>
                                                </h4>
                                                <div class="comment-footer">
                                                    <!-- 按鈕 -->
                                                    {{--<a href="{{ route('course.showCourseStudents', $student_course->id) }}" class="btn btn-danger btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">課程資訊</a>--}}
                                                    <a href="{{ route('courses.showCourseAssignments_Student', ['courses_id' => $student_course->id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看作業</a>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">已結束的課程 </h4>
                                </div>
                                <div class="comment-widgets scrollable">

                                    <!-- Course Loop Start -->
                                @foreach($student_courses_ended as $student_course)

                                    <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/course.png') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h4 class="font-medium">
                                                    {{ $student_course->year }} 年 第 {{ $student_course->semester }} 學期
                                                    <span class="text-muted float-right">結束日期：{{ $student_course->end_date }}</span>
                                                </h4>

                                                <span class="badge badge-pill badge-info float-right"  style="font-size: 100%;">
                                                    {{ $student_course->common_course_name }}
                                                </span>

                                                <h4>
                                                    <span class="m-b-15 d-block" style="margin-top: 10px;">{{ $student_course->name }}</span>
                                                </h4>
                                                <div class="comment-footer">
                                                    <!-- 按鈕 -->
                                                    {{--<a href="{{ route('course.showCourseStudents', $student_course->id) }}" class="btn btn-danger btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">課程資訊</a>--}}

                                                    <a href="{{ route('courses.showCourseAssignments_Student', ['courses_id' => $student_course->id]) }}" class="btn btn-cyan btn-md" role="button" aria-pressed="true" style="margin-top: 3px;">查看作業</a>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach

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
                All Rights Reserved by Chun-Kai Kao. Technical problem please contact: cg.workst@gmail.com
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
