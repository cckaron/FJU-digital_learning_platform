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

        @include('layouts.partials.pageBreadCrumb', ['title' => '作業詳情'])

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
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead align="center">
                                            <tr>
                                                <th>批改狀態</th>
                                                <th>姓名</th>
                                                <th>學號</th>
                                                <th>分數</th>
                                                <th>學習主題</th>
                                                <th>附檔</th>
                                                <th>上傳時間</th>
                                            </tr>
                                            </thead>
                                            <tbody align="center">

                                            @for($i=0; $i< count($student_ids); $i++)
                                            <tr>
                                                <td>
                                                @if($assignment_status == 1) {{-- 作業進行中 --}}
                                                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            學生未繳交
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            尚未批改
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                        <span class="badge badge-pill badge-primary m-b-5"  style="font-size: 100%;">
                                                            已批改
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                                                        <!-- It should not be happened-->
                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                        <!-- It should not be happened-->
                                                    @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放繳交--}}
                                                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                            等待學生重繳
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            學生已重繳
                                                        </span>
                                                    @endif

                                                @elseif($assignment_status == 0) {{-- 作業已經結束 --}}
                                                <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                                                    作業截止
                                                </span>
                                                    @if($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 未繳交--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            學生未繳交作業
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5 m-l-5"  style="font-size: 100%;">
                                                            已批改
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            等待學生補交
                                                        </span>
                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            學生已補繳
                                                        </span>
                                                    @endif
                                                @endif
                                                </td>

                                                <td><a class="link" href="{{ route('user.studentDetail', ['student_id' => $student_ids[$i]]) }}">{{ $student_names[$i] }}</a></td>
                                                <td>{{ $student_ids[$i] }}</td>

                                                <td>
                                                    @if($scores[$i] < 60)
                                                        <span style="color:red; font-size: 20px;">{{ $scores[$i] }}</span>
                                                    @elseif($scores[$i] >= 60)
                                                        <span style="color:blue; font-size: 20px;"> {{ $scores[$i] }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {!! $remark[$i] !!}
                                                </td>
                                                <td>
                                                    @if(count($file_names[$i]) != 0)
                                                        @for($k=0; $k< count($file_names[$i]); $k++)
                                                            {{ $k+1 }}.
                                                            <a href="{{ route('dropZone.downloadAssignment', ['first' => 'public', 'second'=> $student_ids[$i], 'third' => $assignment_id, 'fourth' => $file_names[$i][$k]]) }}">
                                                                {{ $file_names[$i][$k] }}
                                                            </a>
                                                            <br>
                                                        @endfor
                                                    @else
                                                        無
                                                    @endif
                                                </td>
                                                <td>{{ $updated_at[$i] }}</td>
                                            </tr>
                                            @endfor
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
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- for popup window-->
                                    <div id="correctModal" class="modal fade" role="dialog">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post" id="correct_form">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">批改作業</h4>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            &times;
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        {{ csrf_field() }}
                                                        <span id="form_output"></span>
                                                        <div class="form-group">
                                                            <label>分數</label>
                                                            <input type="text" name="score" class="form-control"/>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>教師評語</label>
                                                            <input type="text" name="comment" class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="student_assignment_id" id="student_assignment_id" value="" />
                                                        {{--<button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;">關閉</button>--}}
                                                        <input type="submit" name="submit" id="action" value="確認" class="btn btn-info">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end popup window -->

                                </div>
                            </div>
                        </div>
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
        for (var i =0; i< {{ count($students_assignments_id) }}; i++){
            (function(){
                var id = '#correct_data'+i;
                var form = '#correct_form';
                var name = '#student_assignment_id_'+i;
                var student_assignment_id = $(name).val();


                $(id).click(function(){
                    var modal = '#correctModal';
                    $(modal).modal('show');
                    $('#student_assignment_id').val(student_assignment_id);
                });


                $(form).on('submit', function(event){
                    event.preventDefault();
                    var form_data = $(this).serialize();
                    $.ajax({
                        url:'{{ route('ajax.correctAssignment') }}',
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
                })
            })();
        }

        $('#correctModal').on('hidden.bs.modal', function () {
            location.reload();
        })

    </script>

    <script>
        /****************************************
         *       Basic Table                   *
         ****************************************/

        var table = $('#zero_config').DataTable({
            order: [[ 2, "asc" ]],
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
                    title: '作業',
                    text: '複製作業內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '作業',
                    filename: '作業',
                    text: '匯出 EXCEL',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '作業' );
                    }
                },
                {
                    extend: 'csv',
                    title: '作業',
                    filename: '作業',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '作業',
                    filename: '作業',
                    text: '列印/匯出PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
            ],
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "全部"]],
            columnDefs: [
                { "width": "10%", "targets": 0 },
                { "width": "10%", "targets": 1 },
                { "width": "10%", "targets": 2 },
                { "width": "10%", "targets": 3 },
                { "width": "20%", "targets": 4 },
                { "width": "20%", "targets": 5 },
                { "width": "20%", "targets": 6 },

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

@endsection
