@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/jquery.timepicker.min.css') }}" rel="stylesheet" />

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

        @include('layouts.partials.pageBreadCrumb', ['title' => '新增作業'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('Assignment.createAssignment') }}" method="post" id="createAssignment">

                    <!-- editor -->
                    <div class="row">

                        @include('layouts.partials.returnMessage')

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">課程名稱</label>
                                        <div class="col-md-3">
                                            <select id="courseName" name="courseName" class="select2 form-control custom-select" style="width: 100%; height:36px;" required>
                                                @for($i=0; $i<count($course_names); $i++)
                                                    <option>{{ $course_names[$i] }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6 m-t-10">
                                            <h4>
                                                隸屬共同課程：
                                                <span style="color:blue" id="common_course_name">
                                                    @if(count($common_courses_name) > 0)
                                                        {{ $common_courses_name[0] }}
                                                    @endif
                                                </span>

                                            </h4>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <label class="col-md-3" for="assignmentName">作業名稱</label>
                                        <div class="col-md-9">
                                            <input type="text" id="assignmentName" class="form-control" placeholder="作業名稱" name="assignmentName" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-10" for="userAccount">作業內容</label>
                                        <div class="col-md-8">
                                            <div id="editor" style="height: 300px;">

                                            </div>

                                            <textarea id="assignmentContent" name="assignmentContent" hidden>  </textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">開放繳交時間</label>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="datepicker-start" name="assignmentStart" placeholder="日期" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="timepicker-start" name="assignmentStartTime" placeholder="時間" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar-times"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">截止時間</label>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="datepicker-end" name="assignmentEnd" placeholder="日期" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="timepicker-end" name="assignmentEndTime" placeholder="時間" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar-times"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-9" for="userAccount">佔分比例</label>
                                        <div class="col-md-5">
                                            <div class="input-group mb-3">
                                                <input type="text" id="userAccount" class="form-control" placeholder="ex. 輸入 25 代表 25%" name="assignmentPercentage" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3">是否公佈成績？</label>
                                        <div class="col-md-9">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="notAnnounceScore" name="notAnnounceScore">
                                                <label class="custom-control-label" for="notAnnounceScore">不公布</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="border-top">
                                    <div class="card-body">
                                        <input type="submit" class="btn btn-primary">
                                    </div>
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
    <script src="{{ URL::to('js/jquery.timepicker.min.js') }}"></script>

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


        /*datepicker*/
        $('#datepicker-start').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",

        });
        $('#datepicker-end').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",
        });

        $('#timepicker-start').timepicker(
            { 'scrollDefault': 'now',am: '上午', pm: '下午', AM: '上午', PM: '下午', decimal: '.', mins: 'mins', hr: 'hr', hrs: 'hrs' });
        $('#timepicker-end').timepicker(
            { 'scrollDefault': 'now',am: '上午', pm: '下午', AM: '上午', PM: '下午', decimal: '.', mins: 'mins', hr: 'hr', hrs: 'hrs' });

        var quill = new Quill('#editor', {
            theme: 'snow',
        });

        $("#createAssignment").on("submit",function(){
            var myEditor = document.querySelector('#editor');
            var html = myEditor.children[0].innerHTML;

            $("#assignmentContent").val(html);
        })
    </script>

    <script>

        $('#courseUsers').DataTable({
            processing:true,
            serverSide:true,
            ajax: '{!! route('get.courseUsers') !!}',
            columns: [
                { data: 'checkbox', name: 'checkbox'},
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name'},
                { data: 'type', name: 'type'},
                { data: 'created_at', name: 'created_at'},
            ]
        });

    </script>

    <script>

        var courseName = $('#courseName');

        var commonCourseName = {!! $common_courses_name !!};

        courseName.change(function () {
            var index = courseName[0].selectedIndex;
            document.getElementById("common_course_name").innerHTML= commonCourseName[index];

        })
    </script>

    <!-- close autocomplete of datetime picker -->
    <script>
        $('#datepicker-start').attr('autocomplete','off');
        $('#datepicker-end').attr('autocomplete','off');
    </script>

@endsection
