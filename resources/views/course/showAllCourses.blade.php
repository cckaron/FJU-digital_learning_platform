@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />

    <style>
        input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
            -webkit-box-sizing:border-box;
            -moz-box-sizing: border-box;
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

        @include('layouts.partials.pageBreadCrumb', ['title' => '所有課程'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <!-- editor -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered display" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>課程名稱</th>
                                            <th>共同課程</th>
                                            <th>學年</th>
                                            <th>學期</th>
                                            <th>開課日期</th>
                                            <th>結束日期</th>
                                            <th>上次修改時間</th>
                                            <th>狀態</th>
                                            <th>動作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($courses as $course)
                                            <tr align="center">
                                                <td id="name">{{ $course->course_name }}</td>
                                                <td>{{ $course->common_course->name }}</td>
                                                <td>{{ $course->common_course->year }}</td>
                                                <td>{{ $course->common_course->semester }}</td>
                                                <td>{{ $course->common_course->start_date }}</td>
                                                <td>{{ $course->common_course->end_date }}</td>
                                                <td>{{ $course->common_course->updated_at->diffForHumans() }}</td>
                                                <td>
                                                    @if($course->common_course->status == 1)
                                                        <p style="color: blue">進行中</p>
                                                    @else
                                                        <p style="color: green">已結束</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('course.showCourseStudents', ['courses_id' => $course->id]) }}" class="btn btn-info btn-md">
                                                        查看詳情
                                                    </a>
                                                    <button name="add" class="btn btn-primary" data-toggle="modal" data-target="#changeModal" type="submit" data-course-id="{{ $course->real_id }}">
                                                        編輯
                                                    </button>
                                                    <a href="{{ route('course.delete', ['courses_id' => $course->id]) }}" class="btn btn-danger btn-md" onclick="return confirm('該課程資料將會一併刪除，確定刪除?')">
                                                        刪除
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- start ajax correct assignment window-->
                    <div id="changeModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" id="change_form">
                                    <div class="modal-header">
                                        <h4 class="modal-title">編輯課程</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{ csrf_field() }}
                                        <span id="form_output"></span>
                                        <div class="form-group">
                                            <label>課程名稱</label>
                                            <input type="text" id="modal_name" name="name" class="form-control" required/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="course_id" id="course_id" value="" />
                                        <input type="submit" name="submit" id="action" value="確認" class="btn btn-info">
                                        <p></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- end ajax correct assignment window -->


                </div>
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

    <script>

        // $.fn.dataTable.enum( [
        //     '產業創新(一)',
        //     '產業創新(二)',
        //     '產業創新(三)',
        //     '產業創新(四)',
        //     '產業創新(五)',
        //     '產業創新(六)',
        //     '產業創新(七)',
        //     '產業創新(八)',
        // ] );

        var table = $('#zero_config').DataTable({
            autoWidth: false,
            buttons: [
                {
                    extend: 'colvis',
                    text: '顯示/隱藏欄位',
                    // columns: ':gt(0)'
                    // this will make first column cannot be hided
                },
                {
                    extend: 'copy',
                    title: '所有課程',
                    text: '複製表格內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '所有課程',
                    filename: '所有課程',
                    text: '匯出 excel',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '所有課程' );
                    }
                },
                {
                    extend: 'csv',
                    title: '所有課程',
                    filename: '所有課程',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '所有課程',
                    filename: '所有課程',
                    text: '列印/匯出PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                },

            ],
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "全部"]],
            columnDefs: [
                { "width": "5%", "targets": 0 },
                { "width": "5%", "targets": 1 },
                { "width": "5%", "targets": 2 },
                { "width": "5%", "targets": 3 },
                { "width": "5%", "targets": 4 },
                { "width": "5%", "targets": 5 },
                { "width": "5%", "targets": 6 },
                { "width": "5%", "targets": 7 },
                { "width": "10%", "targets": 8 },
            ],
            language: {
                "processing":   "處理中...",
                "loadingRecords": "載入中...",
                "lengthMenu":   "顯示 _MENU_ 項結果",
                "zeroRecords":  "沒有符合的結果",
                "info":         "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
                "infoEmpty":    "顯示第 0 至 0 項結果，共 0 項",
                "infoFiltered": "(從 _MAX_ 項結果中過濾)",
                "infoPostFix":  "",
                "search":       "搜尋全部:",
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

        // Setup - add a text input to each footer cell
        $('#zero_config tfoot th').each( function () {
            var title = $(this).text();
            // $(this).html( '<input type="text" placeholder="搜尋 '+title+' 欄位" />' );
            $(this).html( '<input type="text" placeholder="搜尋" />' );

        } );

        var r = $('#zero_config tfoot tr');
        r.find('th').each(function(){
            $(this).css('padding', 8);
        });
        // $('#zero_config thead').append(r);
        r.appendTo($('#zero_config thead'));

        // Apply the search
        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                        .search( this.value )
                        .draw();
                }
            } );
        } );

    </script>

    <script>
        $('#toggle_event_editing button').click(function(){

            /* reverse locking status */
            $('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active btn-light btn-primary');
            $('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-primary btn-light');
        });
    </script>

    <script>
        var form = '#change_form';

        $("#changeModal").on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var course_id = button.data('course-id');
            var common_course = button.parent().parent();
            var common_course_name = common_course.children('#name').html();

            $('#course_id').val(course_id);
            $('#modal_name').val(common_course_name);

            $(form).off().on('submit', function(event){
                event.preventDefault();
                var form_data = $(this).serialize();
                $.ajax({
                    url:'{{ route('course.changeContent') }}',
                    method:"POST",
                    data:form_data,
                    dataType:"json",
                    success:function(data)
                    {
                        if (data.error.length > 0)
                        {
                            var error_html = '';
                            for (var count = 0; count < data.error.length; count++)
                            {
                                error_html += '<div class="alert alert-danger">'+data.error[count]+'</div>';
                            }
                            $('#form_output').html(error_html);
                        }
                        else
                        {
                            $('#form_output').html(data.success);
                        }
                    }
                })
            });
        });


        $('#changeModal').on('hidden.bs.modal', function () {
            location.reload();
        });

    </script>


    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
