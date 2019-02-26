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

        @include('layouts.partials.pageBreadCrumb', ['title' => '批量新增作業'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('assignment.batchCreateAssignments') }}" method="post" id="batchCreateAssignment">

                    <!-- editor -->
                    <div class="row">

                        @include('layouts.partials.returnMessage')
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="margin-bottom: 20px"> 選取課程(可複選) </h4>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>勾選</th>
                                                <th>課程名稱</th>
                                                <th>隸屬共同課程</th>
                                                <th>學年</th>
                                                <th>學期</th>
                                                <th>班級</th>
                                                <th>指導教師</th>
                                                <th>開課日期</th>
                                                <th>結課日期</th>
                                                <th>上次修改時間</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @php($teacher_index_count = 0)
                                            @foreach($common_courses as $common_course)
                                                @foreach($common_course->course as $courses)
                                                    <tr>
                                                        <td>
                                                            <label class="customcheckbox">
                                                                <input type="checkbox" class="listCheckbox" name="courses_id[]" value="{{ $courses->id }}" /><span class="checkmark"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            {{ $courses->name }}
                                                        </td>
                                                        <td>
                                                            {{ $common_course->name }}
                                                        </td>
                                                        <td>
                                                            {{ $common_course->year }}
                                                        </td>
                                                        <td>
                                                            {{ $common_course->semester }}
                                                        </td>
                                                        <td>
                                                            @if($courses->class == 1)
                                                                甲
                                                            @elseif($courses->class == 2)
                                                                乙
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @foreach($teachers_name[$teacher_index_count] as $teacher_name)
                                                                {{ $teacher_name }}
                                                            @endforeach

                                                            @php($teacher_index_count += 1)
                                                        </td>
                                                        <td>
                                                            {{ $common_course->start_date }}                                                    </td>
                                                        <td>
                                                            {{ $common_course->end_date }}                                                    </td>
                                                        <td>
                                                            {{ $courses->updated_at->diffForHumans() }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <h4 style="margin-bottom: 20px"> 作業詳情 </h4>

                                    <div class="form-group row">
                                        <label class="col-md-2 m-t-10" for="userAccount">作業名稱</label>
                                        <div class="col-md-6">
                                            <input type="text" id="userAccount" class="form-control" placeholder="作業名稱" name="assignmentName" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2 m-t-10" for="userAccount">作業內容</label>
                                        <div class="col-md-6">
                                            <div id="editor" style="height: 300px;">

                                            </div>

                                            <textarea id="assignmentContent" name="assignmentContent" hidden>  </textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2 m-t-15">開放繳交時間</label>
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
                                        <label class="col-md-2 m-t-15">截止時間</label>
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
                                        <label class="col-md-2 m-t-10" for="userAccount">佔分比例</label>
                                        <div class="col-md-2">
                                            <div class="input-group mb-3">
                                                <input type="text" id="userAccount" class="form-control" placeholder="ex. 輸入 25 代表 25%" name="assignmentPercentage" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2">是否公佈成績？</label>
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

        $("#batchCreateAssignment").on("submit",function(){
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

    {{--<script>--}}

    {{--var courseName = $('#courseName');--}}

    {{--var commonCourseName = {!! $common_courses_name !!};--}}

    {{--courseName.change(function () {--}}
    {{--var index = courseName[0].selectedIndex;--}}
    {{--document.getElementById("common_course_name").innerHTML= commonCourseName[index];--}}

    {{--})--}}
    {{--</script>--}}

    <!-- close autocomplete of datetime picker -->
    <script>
        $('#datepicker-start').attr('autocomplete','off');
        $('#datepicker-end').attr('autocomplete','off');
    </script>

    <script>
        /****************************************
         *       Basic Table                   *
         ****************************************/
        $('#zero_config').DataTable({
            language: {
                "processing":   "處理中...",
                "loadingRecords": "載入中...",
                "lengthMenu":   "顯示 _MENU_ 項結果",
                "zeroRecords":  "沒有符合的結果",
                "info":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                "infoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                "infoFiltered": "(從 _MAX_ 項結果中過濾)",
                "infoPostFix":  "",
                "search":       "搜尋:",
                "paginate": {
                    "first":    "第一頁",
                    "previous": "上一頁",
                    "next":     "下一頁",
                    "last":     "最後一頁"
                },
                "aria": {
                    "sortAscending":  ": 升冪排列",
                    "sortDescending": ": 降冪排列"
                }
            },
        });
    </script>

@endsection
