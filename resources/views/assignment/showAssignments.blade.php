@extends('layouts.main')

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
                                @for($i=0; $i<count($assignments_processing); $i++)

                                    <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/users/1.jpg') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h6 class="font-medium">{{ $courses_processing[$i][0]->year }} 年 第 {{ $courses_processing[$i][0]->semester }} 學期</h6>
                                                <span class="badge badge-pill badge-info float-right">
                                                指導老師:
                                                    <!-- teacher name -->
                                                    @for($j=0; $j<count($teachers_processing[$i]); $j++)
                                                        {{ $teachers_processing[$i][$j] }}
                                                        @if($j!=count($teachers_processing[$i])-1) , @endif <!-- 逗號 -->
                                                    @endfor

                                                </span>
                                                <span class="m-b-15 d-block">{{ $assignments_processing_name[$i] }}</span>
                                                <div class="comment-footer">
                                                    <span class="text-muted float-right">April 14, 2016</span>
                                                    <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignments_processing_course_id[$i] ,'assignment_id' => $assignments_processing_id[$i]]) }}" class="btn btn-cyan btn-sm" role="button" aria-pressed="true">繳交作業</a>
                                                    <span class="badge badge-pill badge-danger">狀態：未繳交</span>
                                                    <span class="badge badge-pill badge-primary">成績：無</span>
                                                    {{--<button type="button" class="btn btn-success btn-sm">Publish</button>--}}
                                                    {{--<button type="button" class="btn btn-danger btn-sm">Delete</button>--}}
                                                </div>
                                            </div>
                                        </div>

                                    @endfor

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">已完成的作業 </h4>
                                </div>
                                <div class="comment-widgets scrollable">

                                    <!-- Assignment Loop Start -->
                                @for($i=0; $i<count($assignments_finished); $i++)

                                    <!-- Comment Row -->
                                        <div class="d-flex flex-row comment-row m-t-0">

                                            <div class="p-2"><img src="{{ URL::to('images/users/1.jpg') }}" alt="user" width="50" class="rounded-circle"></div>
                                            <div class="comment-text w-100">

                                                <h6 class="font-medium">{{ $courses_finished[$i][0]->year }} 年 第 {{ $courses_finished[$i][0]->semester }} 學期</h6>
                                                <span class="badge badge-pill badge-info float-right">
                                                指導老師:
                                                    <!-- teacher name -->
                                                    @for($j=0; $j<count($teachers_finished[$i]); $j++)
                                                        {{ $teachers_finished[$i][$j] }}
                                                        @if($j!=count($teachers_finished[$i])-1) , @endif <!-- 逗號 -->
                                                    @endfor
                                                </span>
                                                <span class="m-b-15 d-block">{{ $assignments_finished_name[$i] }}</span>
                                                <div class="comment-footer">
                                                    <span class="text-muted float-right">April 14, 2016</span>
                                                    <a href="{{ route('assignment.handInAssignment', ['course_id' => $assignments_finished_course_id[$i] ,'assignment_id' => $assignments_finished_id[$i]]) }}" class="btn btn-cyan btn-sm" role="button" aria-pressed="true">繳交作業</a>
                                                    {{--<button type="button" class="btn btn-success btn-sm">Publish</button>--}}
                                                    {{--<button type="button" class="btn btn-danger btn-sm">Delete</button>--}}
                                                </div>
                                            </div>
                                        </div>

                                    @endfor

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
