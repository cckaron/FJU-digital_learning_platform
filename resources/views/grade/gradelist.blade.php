@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />
    <!-- DropZone JS-->
    <link href="{{ URL::to('css/dropzone.css') }}" rel="stylesheet" />

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

        @include('layouts.partials.pageBreadCrumb', ['title' => '評分表'])

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
                        {{--<div class="card">--}}
                        {{--<div class="card-body">--}}
                        {{--<h5 class="card-title">繳交狀態</h5>--}}
                        {{--<h6 style="padding-top: 10px;">--}}
                        {{--已繳交人數：--}}
                        {{--<span style="color:blue">{{ $finished }}</span>--}}
                        {{--/ {{ $all }}人--}}
                        {{--</h6>--}}
                        {{--@if($finishedHandIn > 0)--}}
                        {{--<h6 style="padding-top: 10px; color:red">提醒：您尚有 {{ $finishedHandIn }} 份作業未批改</h6>--}}
                        {{--@endif--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="card">
                            <div class="card-body">
                                {{--@php(dd($student_ids))--}}
                                {{--@php(dd($student_assignments_id))--}}
                                {{--{{ $student_assignments[0] }}--}}
                                <br>
                                {{--{{ $test }}--}}
                                {{--{{ $assignments }}--}}

                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered display" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>學號</th>
                                            <th>姓名</th>
                                            <th>學生班級</th>
                                            <th>產創編入班級</th>
                                            @php($totalPercentage = 0)
                                            @foreach($assignments as $assignment)
                                                <th> {{ $assignment->name }} ({{ $assignment->percentage }}%)</th>
                                                @php($totalPercentage += $assignment->percentage)
                                            @endforeach
                                            @php($availPercentage = 100 - $totalPercentage)
                                            <th>原始成績</th>
                                            <th>最終成績</th>
                                            <th>備註</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($students as $key => $student)
                                            <tr>
                                                <td>{{ $student->users_id }}</td>
                                                <td><a class="link" href="{{ route('user.studentDetail', ['student_id' => $student->users_id]) }}">{{ $student->users_name }}</a></td>
                                                <td>{{ $student->class }}</td>
                                                <td>{{ $student->common_course_name }}</td>

                                                @foreach($student_assignments[$key] as $student_assignment)
                                                    <td>
                                                        @if($student_assignment->pivot->score < 60)
                                                            <span style="color:red; font-size: 18px;">{{ number_format($student_assignment->pivot->score, 2) }}</span>
                                                        @elseif($student_assignment->pivot->score >= 60)
                                                            <span style="color:blue; font-size: 18px;"> {{ number_format($student_assignment->pivot->score, 2) }}</span>
                                                        @endif
                                                    </td>

                                                    @if($loop->last)
                                                        <td>
                                                            @if($student_assignment->accumulated_score < 60)
                                                                <span style="color:red; font-size: 20px;">{{ number_format($student_assignment->accumulated_score, 2) }}</span>
                                                            @elseif($student_assignment->accumulated_score >= 60)
                                                                <span style="color:blue; font-size: 20px;"> {{ number_format($student_assignment->accumulated_score, 2) }}</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if($student_course_final_score[$key] < 60)
                                                                <span style="color:red; font-size: 20px;">{{ number_format($student_course_final_score[$key], 2) }}</span>
                                                            @elseif($student_course_final_score[$key] >= 60)
                                                                <span style="color:blue; font-size: 20px;"> {{ number_format($student_course_final_score[$key], 2) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $student_assignment->comment }}</td>
                                                    @endif
                                                @endforeach



                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            @foreach($assignments as $assignment)
                                                <th></th>
                                            @endforeach
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <!-- start ajax correct assignment window-->
                        <div id="uploadModal" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">上傳 Excel 檔</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div style="overflow:auto; ">
                                            <form action="{{ route('dropZone.importGrade') }}" class="dropzone" method="post" enctype="multipart/form-data" id="myDropzone">
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end ajax correct assignment window -->

                        <!-- start ajax correct assignment window-->
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
                                            <p>總成績比率 100% (目前可分配的比率為 {{ $availPercentage }}%)</p>
                                            @foreach($assignments as $assignment)
                                                <div class="form-group row" >
                                                    <label class="col-md-3 m-t-9" for="userAccount">{{ $assignment->name }}</label>
                                                    <div class="col-md-5">
                                                        <div class="input-group mb-3">
                                                            <input type="number" step="0.01" class="form-control" value="{{ $assignment->percentage }}" name="assignmentPercentage[]">
                                                            <input type="hidden" name="assignmentID[]" value="{{ $assignment->id }}" required>

                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
                        <!-- end ajax correct assignment window -->
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
    <!-- DropZone JS-->
    <script src="{{ URL::to('js/dropzone.js') }}"></script>

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
                    title: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    text: '複製評分表內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    filename: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    text: '匯出 EXCEL',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表' );
                    }
                },
                {
                    extend: 'csv',
                    title: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    filename: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    filename: '{!! $course->year !!}_{!! $course->semester !!}_{!! $teacher->users_name !!}老師_評分表',
                    text: '列印/匯出PDF',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    text: '匯入成績',
                    action: function ( e, dt, node, config ) {
                        var modal = '#uploadModal';
                        $(modal).modal('show');
                    }
                },
                // {
                //     text: '成績比率設定',
                //     action: function ( e, dt, node, config ) {
                //         var modal = '#updatePercentageModal';
                //         $(modal).modal('show');
                //     }
                // }

            ],
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "全部"]],
            columnDefs: [
                { "width": "15%", "targets": 0 }
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
        Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳 excel 檔案 (副檔名: .xls, .xlsx)";
        Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
        Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
        Dropzone.prototype.defaultOptions.dictFileTooBig = "檔案超出最大檔案限制: 20MB.";
        Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
        Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
        Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
        Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";

        Dropzone.options.myDropzone = {
            maxFilesize: 100,
            maxFiles: 10,
            acceptedFiles: ".xls, .xlsx",
        };

        $('#uploadModal').on('hidden.bs.modal', function () {
            location.reload();
        });

        $('#updatePercentageModal').on('hidden.bs.modal', function () {
            location.reload();
        });

    </script>

    <script>
        var form = '#UpdatePercentageForm';
        $(form).off().on('submit', function(event){
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url:'{{ route('grade.ajax.updatePercentage') }}',
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
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
