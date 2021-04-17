@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-toggle/bootstrap-toggle.min.css') }}" rel="stylesheet" />

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

        @include('layouts.partials.pageBreadCrumb', ['title' => '所有共同課程'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
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

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered display" style="width:100%">
                                            <thead>
                                            <tr>
                                                <th>共同課程名稱</th>
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
                                            @foreach($common_courses as $common_course)
                                                <tr align="center">
                                                    <td id="name">{{ $common_course->name }}</td>
                                                    <td id="year">{{ $common_course->year }}</td>
                                                    <td id="semester">{{ $common_course->semester }}</td>
                                                    <td id="start_date">{{ $common_course->start_date }}</td>
                                                    <td id="end_date">{{ $common_course->end_date }}</td>
                                                    <td>{{ $common_course->updated_at->diffForHumans() }}</td>
                                                    <td>
                                                        <input id="status-toggle" type="checkbox" data-common-course-id="{{ $common_course->id }}" data-toggle="toggle" data-on="進行中" data-off="已結束" @if($common_course->status == 1) checked @endif>
                                                    </td>
                                                    <td>
                                                        <button name="add" class="btn btn-primary" data-toggle="modal" data-target="#changeModal" type="submit" data-common-course-id="{{ $common_course->id }}">
                                                            編輯
                                                        </button>

                                                        <a href="{{ route('commonCourse.delete', ['id' => $common_course->id]) }}" class="btn btn-danger btn-md" onclick="return confirm('該課程資料將會一併刪除，確定刪除?')">
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
                                            <h4 class="modal-title">編輯共同課程</h4>
                                            <button type="button" class="close" data-dismiss="modal">
                                                &times;
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            {{ csrf_field() }}
                                            <span id="form_output"></span>
                                            <div class="form-group">
                                                <label>共同課程名稱</label>
                                                <input type="text" id="modal_name" name="name" class="form-control" required/>
                                            </div>
                                            <div class="form-group">
                                                <label>學年</label>
                                                <input type="text" id="modal_year" name="year" class="form-control" required/>
                                            </div>
                                            <div class="form-group">
                                                <label>學期</label>
                                                <input type="text" id="modal_semester" name="semester" class="form-control" required/>
                                            </div>
                                            <div class="form-group">
                                                <label>開課日期</label>
                                                <input type="text" id="modal_start_date" name="start_date" placeholder="開課時間" required>
                                            </div>
                                            <div class="form-group">
                                                <label>結束日期</label>
                                                <input type="text" id="modal_end_date" name="end_date" placeholder="課程結束時間" required>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="common_course_id" id="common_course_id" value="" />
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
    <script src="{{ URL::to('libs/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ URL::to('libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <script>
        /*datepicker*/

        $('#modal_start_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",

        });
        $('#modal_end_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",
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
            paging: false,
            buttons: [
                {
                    extend: 'colvis',
                    text: '顯示/隱藏欄位',
                    // columns: ':gt(0)'
                    // this will make first column cannot be hided
                },
                {
                    extend: 'copy',
                    title: '所有共同課程',
                    text: '複製表格內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '所有共同課程',
                    filename: '所有共同課程',
                    text: '匯出 excel',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '所有共同課程' );
                    }
                },
                {
                    extend: 'csv',
                    title: '所有共同課程',
                    filename: '所有共同課程',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '所有共同課程',
                    filename: '所有共同課程',
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
                { "width": "10%", "targets": 7 },
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
        $(function() {
            $('[id="status-toggle"]').change(function() {
                var status = $(this).prop('checked')? 1 : 0;
                var common_course_id = $(this).data('common-course-id');
                console.log(common_course_id);

                $.ajax({
                    type:'POST',
                    dataType:'JSON',
                    url:'{{ route('common_courses.changeStatus') }}',
                    data: {'status': status, 'common_course_id': common_course_id},
                    success:function(data)
                    {
                        console.log('common course '+data.cmcsid+' is changed to '+ data.status)
                    }
                });
            })
        })
    </script>

    <script>
        var form = '#change_form';

        $("#changeModal").on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var common_course_id = button.data('common-course-id');
            var common_course = button.parent().parent();
            var common_course_name = common_course.children('#name').html();
            var common_course_year = common_course.children('#year').html();
            var common_course_semester = common_course.children('#semester').html();
            var common_course_start_date = common_course.children('#start_date').html();
            var common_course_end_date = common_course.children('#end_date').html();

            $('#common_course_id').val(common_course_id);
            $('#modal_name').val(common_course_name);
            $('#modal_year').val(common_course_year);
            $('#modal_semester').val(common_course_semester);
            $('#modal_start_date').val(common_course_start_date);
            $('#modal_end_date').val(common_course_end_date);

            $(form).off().on('submit', function(event){
                event.preventDefault();
                var form_data = $(this).serialize();
                $.ajax({
                    url:'{{ route('common_courses.changeContent') }}',
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
