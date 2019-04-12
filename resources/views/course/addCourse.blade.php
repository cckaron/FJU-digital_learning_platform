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

            @include('layouts.partials.pageBreadCrumb', ['title' => '新增課程'])

    <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
                <div class="container-fluid">
                    <!-- ============================================================== -->
                    <!-- Start Page Content -->
                    <!-- ============================================================== -->

                    <form action="{{ route('course.addCourse') }}" method="post">

                    <!-- editor -->
                    <div class="row">

                        @include('layouts.partials.returnMessage')

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        勾選課程學生
                                    </h5>

                                    <div class="table-responsive">
                                        <table id="courseUsers" class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>勾選</th>
                                                <th>學號</th>
                                                <th>姓名</th>
                                                <th>年級</th>
                                                <th>班級</th>
                                                <th>帳號建立時間</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6"></div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">填寫課程資訊</h5>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">隸屬共同課程</label>
                                        <div class="col-md-9">
                                            <select name="common_courses_name" class="select2 form-control m-t-15" style="height: 36px;width: 100%;" required>
                                                @for($i=0; $i< count($common_courses_name); $i++)
                                                    <option> {{ $common_courses_name[$i] }} </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3" for="courseName">課程名稱</label>
                                        <div class="col-md-9">
                                            <input type="text" id="courseName" class="form-control" placeholder="課程名稱" name="courseName" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-10" for="courseName">班級</label>
                                        <div class="col-md-9">
                                            <select name="courseClass" class="select2 form-control m-t-15" style="height: 36px;width: 100%;" required>
                                                <option value=3> 不分班</option>
                                                <option value=1> 甲 </option>
                                                <option value=2> 乙 </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">授課教師</label>
                                        <div class="col-md-9">
                                            <select name="courseTeachers[]" class="select2 form-control m-t-15" multiple="multiple" style="height: 36px;width: 100%;" required>
                                                <optgroup label="現任教師">
                                                    @foreach($teachers->chunk(1) as $teacherChunk)
                                                        @foreach($teacherChunk as $teacher)
                                                            <option> {{ $teacher->users_name }} </option>
                                                        @endforeach
                                                    @endforeach
                                                </optgroup>
                                            </select>
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

    </script>

    <script>

        $('#courseUsers').DataTable({
            processing:true,
            //如果要用serverside, 就必須把paging設為false,否則被勾選的checkbox會因為換頁而消失

            //serverSide:true,
            //paging: false,
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
                },
            },
            ajax: '{!! route('get.courseUsers') !!}',
            columns: [
                { data: 'checkbox', name: 'checkbox'},
                { data: 'users_id', name: 'users_id' },
                { data: 'users_name', name: 'users_name'},
                { data: 'grade', name: 'grade'},
                { data: 'class', name: 'class'},
                { data: 'created_at', name: 'created_at'},
            ]
        });

    </script>
@endsection
