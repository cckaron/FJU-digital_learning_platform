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

        @include('layouts.partials.pageBreadCrumb', ['title' => '所有作業'])

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
                                            <th>作業名稱</th>
                                            <th>作業狀態</th>
                                            <th>共同課程</th>
                                            <th>共同課程狀態</th>
                                            <th>課程</th>
                                            <th>學年</th>
                                            <th>學期</th>
                                            <th>佔分比例</th>
                                            <th>開課日期</th>
                                            <th>結束日期</th>
                                            <th>上次修改時間</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($assignments as $assignment)
                                            <tr align="center">
                                                <td>{{ $assignment->assignment_name }}</td>
                                                <td>
                                                    @if($assignment->assignment_status == 1)
                                                        <p style="color: blue">進行中</p>
                                                    @else
                                                        <p style="color: green">已截止</p>
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->common_course_name }}</td>
                                                <td>
                                                    @if($assignment->common_course_status == 1)
                                                        <p style="color: blue">進行中</p>
                                                    @else
                                                        <p style="color: green">已結束</p>
                                                    @endif
                                                </td>
                                                <td>{{ $assignment->course_name }}</td>
                                                <td>{{ $assignment->year }}</td>
                                                <td>{{ $assignment->semester }}</td>
                                                <td>{{ $assignment->assignment_percentage }}%</td>
                                                <td>{{ $assignment->start_date }}</td>
                                                <td>{{ $assignment->end_date }}</td>
                                                <td>{{ $assignment->updated_at->diffForHumans() }}</td>
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
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- start ajax set percentage window-->
                    <div id="updatePercentageModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" id="UpdatePercentageForm">
                                    <div class="modal-header">
                                        <h4 class="modal-title">成績比率設定</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            &times;
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        {{ csrf_field() }}
                                        <span id="form_output"></span>
                                        @php($availPercentage = $assignments_a4[0]->percentage +
                                        $assignments_attendance[0]->percentage +
                                        $assignments_ppt[0]->percentage +
                                        $assignments_word[0]->percentage
                                        )
                                        <p>總成績比率 {{ $availPercentage }}% (目前可分配的比率為 {{ 100-$availPercentage }}%)</p>
                                        <div class="form-group row" >
                                            <label class="col-md-3 m-t-9" for="userAccount">{{ $assignments_a4[0]->name }}</label>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3">
                                                    <input type="number" step="0.01" class="form-control" value="{{ $assignments_a4[0]->percentage }}" name="assignmentPercentage[]">
                                                    @foreach($assignments_a4_id as $value)
                                                        <input type="hidden" name="assignments_a4_id[]" value="{{ $value }}" required>
                                                    @endforeach
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <label class="col-md-3 m-t-9" for="userAccount">{{ $assignments_attendance[0]->name }}</label>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3">
                                                    <input type="number" step="0.01" class="form-control" value="{{ $assignments_attendance[0]->percentage }}" name="assignmentPercentage[]">
                                                    @foreach($assignments_attendance_id as $value)
                                                        <input type="hidden" name="assignments_attendance_id[]" value="{{ $value }}" required>
                                                    @endforeach
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <label class="col-md-3 m-t-9" for="userAccount">{{ $assignments_ppt[0]->name }}</label>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3">
                                                    <input type="number" step="0.01" class="form-control" value="{{ $assignments_ppt[0]->percentage }}" name="assignmentPercentage[]">

                                                    @foreach($assignments_ppt_id as $value)
                                                        <input type="hidden" name="assignments_ppt_id[]" value="{{ $value }}" required>
                                                    @endforeach
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" >
                                            <label class="col-md-3 m-t-9" for="userAccount">{{ $assignments_word[0]->name }}</label>
                                            <div class="col-md-5">
                                                <div class="input-group mb-3">
                                                    <input type="number" step="0.01" class="form-control" value="{{ $assignments_word[0]->percentage }}" name="assignmentPercentage[]">

                                                    @foreach($assignments_word_id as $value)
                                                        <input type="hidden" name="assignments_word_id[]" value="{{ $value }}" required>
                                                    @endforeach
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
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
                    <!-- end ajax set percentage window -->

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
            buttons: [
                {
                    extend: 'colvis',
                    text: '顯示/隱藏欄位',
                    // columns: ':gt(0)'
                    // this will make first column cannot be hided
                },
                {
                    extend: 'copy',
                    title: '所有作業',
                    text: '複製表格內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '所有作業',
                    filename: '所有作業',
                    text: '匯出 excel',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '所有作業' );
                    }
                },
                {
                    extend: 'csv',
                    title: '所有作業',
                    filename: '所有作業',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '所有作業',
                    filename: '所有作業',
                    text: '列印/匯出PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    text: '當學期成績比率設定',
                    action: function ( e, dt, node, config ) {
                        var modal = '#updatePercentageModal';
                        $(modal).modal('show');
                    }
                }
            ],
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "全部"]],
            columnDefs: [

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
        var form = '#UpdatePercentageForm';
        $(form).off().on('submit', function(event){
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url:'{{ route('grade.ajax.updatePercentage_admin') }}',
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
                        console.log(data.myid);
                    }
                }
            })
        });

        $('#updatePercentageModal').on('hidden.bs.modal', function () {
            location.reload();
        });
    </script>
@endsection
