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

        @include('layouts.partials.pageBreadCrumb', ['title' => '作業批改'])

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

                            <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered display" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>批改狀態</th>
                                            <th>共同課程</th>
                                            <th>作業名稱</th>
                                            <th>姓名</th>
                                            <th>學號</th>
                                            <th>分數</th>
                                            <th>主題</th>
                                            <th>附檔</th>
                                            <th>上傳時間</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @for($i=0; $i< count($student_assignments_id); $i++)
                                            <tr>
                                                @if($common_courses_status[$i] == 1) {{-- 課程進行中 --}}
                                                    @if($scores[$i] == null)
                                                        <td>
                                                            {{--<span class="badge badge-pill badge-info float-left"  style="font-size: 100%;">--}}
                                                                {{--尚未批改--}}
                                                            {{--</span>--}}
                                                            <input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-success" value="批改"/>
                                                            <input hidden id="student_assignment_id_{{ $i }}" value="{{ $student_assignments_id[$i] }}"/>
                                                        </td>
                                                    @else
                                                        <td>

                                                            <span>
                                                                @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                                                                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            未批改
                                                                        </span>
                                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            未批改
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input hidden name="status" value=6 /> {{-- 學生作業狀態設為:開放重繳 --}}
                                                                        <input id="btn_student_assignment_id_{{ $student_assignments_id[$i] }}" class="btn btn-sm btn-secondary m-b-5" onclick="return confirm('確定開放重繳作業?')" type="submit"  value="開放重繳"/>
                                                                        {{ csrf_field() }}
                                                                        </form>
                                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            已批改
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input hidden name="status" value=6 /> {{-- 學生作業狀態設為:開放重繳 --}}
                                                                            <input id="btn_student_assignment_id_{{ $student_assignments_id[$i] }}" class="btn btn-sm btn-secondary m-b-5" onclick="return confirm('確定開放重繳作業?')" type="submit"  value="開放重繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            等待學生補繳
                                                                        </span>
                                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            學生已補繳
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input hidden name="status" value=4 /> {{-- 學生作業狀態設為:開放重繳 --}}
                                                                            <input id="btn_student_assignment_id_{{ $student_assignments_id[$i] }}" class="btn btn-sm btn-secondary m-b-5" onclick="return confirm('確定開放重繳作業?')" type="submit"  value="要求再度補繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放繳交--}}
                                                                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                            已開放重繳
                                                                        </span>
                                                                    @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            學生已重繳
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input hidden name="status" value=6 /> {{-- 學生作業狀態設為:開放重繳 --}}
                                                                            <input id="btn_student_assignment_id_{{ $student_assignments_id[$i] }}" class="btn btn-sm btn-secondary m-b-5" onclick="return confirm('確定要求再度重繳作業?')" type="submit"  value="要求再度重繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @endif

                                                                @elseif($assignments_status[$i] == 0) {{-- 作業已經結束 --}}
                                                                    @if($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            已批改
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="status" value=4 /> {{-- 學生作業狀態設為:補繳 --}}
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input class="btn btn-sm btn-secondary m-b-5" type="submit" onclick="return confirm('確定開放補繳作業?')" value="開放補繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 未繳交--}}
                                                                         <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            學生未繳交作業
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="status" value=4 /> {{-- 學生作業狀態設為:補繳 --}}
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input class="btn btn-sm btn-secondary m-b-5" type="submit" onclick="return confirm('確定開放補繳作業?')" value="開放補繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 未繳交--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            等待學生補交
                                                                        </span>
                                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 未繳交--}}
                                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                            學生已補繳
                                                                        </span>
                                                                        <form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">
                                                                            <input hidden name="status" value=4 /> {{-- 學生作業狀態設為:補繳 --}}
                                                                            <input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>
                                                                            <input class="btn btn-sm btn-secondary m-b-5" type="submit" onclick="return confirm('確定開放補繳作業?')" value="開放補繳"/>
                                                                            {{ csrf_field() }}
                                                                        </form>
                                                                    @endif
                                                                @endif
                                                            </span>
                                                            <span>
                                                                <input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-sm btn-danger" value="修改"/>
                                                            </span>

                                                            <input hidden id="student_assignment_id_{{ $i }}" value="{{ $student_assignments_id[$i] }}"/>
                                                        </td>
                                                @endif
                                                @elseif($common_courses_status[$i] == 0) {{-- 課程已經結束 --}}
                                                    @if($scores[$i] == null)
                                                        <td>
                                                            <h6>課程已結束</h6>
                                                            {{--<input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-success" value="批改"/>--}}
                                                            {{--<input hidden id="student_assignment_id_{{ $i }}" value="{{ $student_assignments_id[$i] }}"/>--}}
                                                        </td>
                                                    @else
                                                        <td>
                                                            @if($student_assignment_status[$i] == 3 or 6)
                                                                <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                    已批改
                                                                </span>
                                                            @elseif($student_assignment_status[$i] == 2)
                                                                <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                    教師未批改
                                                                </span>
                                                            @elseif($student_assignment_status[$i] == 4)
                                                                <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                    未繳交
                                                                </span>
                                                            @elseif($student_assignment_status[$i] == 5)
                                                                <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                    已批改(補繳)
                                                                </span>
                                                            @endif

                                                            <span>
                                                                @if($assignments_status[$i] == 1) {{-- 作業進行中(錯誤) --}}
                                                                    {{--<p>發生錯誤</p>--}}
                                                                @elseif($assignments_status[$i] == 0) {{-- 作業已經結束 --}}
                                                                {{--<form id="openHandInAssignment" method="post" action="{{ route('ajax.openHandInAssignment') }}">--}}
                                                                        {{--<input hidden name="status" value=4 /> --}}{{-- 學生作業狀態設為:補繳 --}}
                                                                    {{--<input hidden name="student_assignment_id" value="{{ $student_assignments_id[$i] }}"/>--}}
                                                                    {{--<input class="btn btn-sm btn-primary m-b-5" type="submit" onclick="return confirm('確定開放補繳作業?')" value="開放補繳"/>--}}
                                                                    {{--{{ csrf_field() }}--}}
                                                                    {{--</form>--}}
                                                                    <p>課程已結束</p>
                                                                @endif
                                                            </span>
                                                            <span>
                                                                {{--<input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-sm btn-danger" value="修改"/>--}}
                                                            </span>

                                                            <input hidden id="student_assignment_id_{{ $i }}" value="{{ $student_assignments_id[$i] }}"/>
                                                        </td>
                                                @endif
                                                @endif

                                                <td>{{ $common_courses_name[$i] }}</td>
                                                    <td>{{ $assignments_name[$i] }}</td>

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
                                                            <a href="{{ route('dropZone.downloadAssignment', ['first' => 'public', 'second'=> $student_ids[$i], 'third' => $student_assignment_assignments_id[$i], 'fourth' => $file_names[$i][$k]]) }}">
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
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <!-- start ajax correct assignment window-->
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
                                <!-- end ajax correct assignment window -->

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
        for (var i =0; i< {{ count($student_assignments_id) }}; i++){
            (function(){
                var id = '#correct_data'+i;
                var form = '#correct_form';
                var name = '#student_assignment_id_'+i;
                var student_assignment_id = $(name).val();

                var form2 = '#openHandInAssignment';

                $(id).click(function(){
                    var modal = '#correctModal';
                    $(modal).modal('show');
                    $('#student_assignment_id').val(student_assignment_id);
                    $(id).val("已批改，請重新整理頁面");

                });


                $(form).off().on('submit', function(event){
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
                });

                $(form2).off().on('submit', function(event){
                    event.preventDefault();
                    var form_data = $(this).serialize();
                    $.ajax({
                        url:'{{ route('ajax.openHandInAssignment') }}',
                        method:"POST",
                        data:form_data,
                        dataType:"json",
                        success:function(data)
                        {
                            if (data.error.length > 0)
                            {
                                console.log(data.error)
                            }
                            else
                            {
                                console.log(data.success);
                                var myid = '#btn_student_assignment_id_'+data.id;
                                console.log(myid);
                                document.getElementById(myid).value = '開放成功!';
                                // btn.prop('value', '開放成功!');
                            }
                        }
                    })
                });
            })();
        }

        $('#correctModal').on('hidden.bs.modal', function () {
            // location.reload();
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
                // {
                //   extend: 'copy',
                //     text: '複製表格內容',
                //     exportOptions: {
                //         columns: ':visible'
                //     },
                // },
                {
                    extend: 'excelHtml5',
                    filename: '作業批改',
                    text: '匯出 EXCEL',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '匯出表單' );
                    }
                },
                {
                    extend: 'csv',
                    filename: '作業批改',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    text: '列印/匯出PDF',
                    filename: '作業批改',
                    exportOptions: {
                        columns: ':visible'
                    },
                },

            ],
            dom: 'lBfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "全部"]],
            columnDefs: [
                { "width": "15%", "targets": 0 },
                { "width": "10%", "targets": 1 },
                { "width": "10%", "targets": 2 },
                { "width": "10%", "targets": 3 },
                { "width": "10%", "targets": 4 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 },
                { "width": "20%", "targets": 7 },
                { "width": "10%", "targets": 8 }

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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

@endsection
