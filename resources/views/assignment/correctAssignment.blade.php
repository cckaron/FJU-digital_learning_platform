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

        .btn-href {
            border: none;
            outline: none;
            background: none;
            cursor: pointer;
            color: red;
            padding: 0;
            text-decoration: none;
            font-family: inherit;
            font-size: inherit;
            height: auto;
            width: auto;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;

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
                                            <th>動作</th>
                                            <th>作業狀態</th>
                                            <th>學生繳交狀態</th>
                                            <th>共同課程</th>
                                            <th>作業名稱</th>
                                            <th>姓名</th>
                                            <th>學號</th>
                                            <th>主題</th>
                                            <th>分數</th>
                                            <th>評語</th>
                                            <th>附檔</th>
                                            <th>上傳時間</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @for($i=0; $i< count($student_assignments_id); $i++)
                                            <tr>
                                                <!-- First td START-->
                                                <td>
                                                @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                                                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改 </b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                                                            <b id="bold_rehandIn">開放重繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 批改 </b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                                                            <b id="bold_rehandIn">開放重繳</b>
                                                        </a>
                                                        <br>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 重新批改 </b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 開放補繳中--}}
                                                        <!-- It should not be happened-->
                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                        <!-- It should not be happened-->
                                                    @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改 </b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定要求再度重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                                                            <b id="bold_rehandIn">要求再度重繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 批改 </b>
                                                        </button>
                                                    @endif
                                                @else {{-- 作業已截止 --}}
                                                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放補繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 4]) }}">
                                                            <b id="bold_rehandIn">開放補繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改</b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放補繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 4]) }}">
                                                            <b id="bold_rehandIn">要求再度補繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">批改</b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放補繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 4]) }}">
                                                            <b id="bold_rehandIn">開放補繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">重新批改</b>
                                                        </button>
                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">直接批改</b>
                                                        </button>
                                                        {{--<span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">--}}
                                                            {{--催繳--}}
                                                        {{--</span>--}}
                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                        <a id="rehandIn" class="btn-href" onclick="return confirm('確定要求再度補繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 4]) }}">
                                                            <b id="bold_rehandIn">要求再度補繳</b>
                                                        </a>
                                                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                                                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">批改</b>
                                                        </button>
                                                    @endif
                                                @endif
                                                </td>
                                                <!-- First td END-->

                                                <!-- Second td START-->
                                                <td>
                                                    @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                                                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                            進行中
                                                        </span>
                                                    @else {{-- 作業已截止 --}}
                                                        <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                                                            已截止
                                                        </span>
                                                    @endif
                                                </td>
                                                <!-- Second td END-->


                                                <!-- Third td START-->
                                                <td>
                                                    @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                                                        @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                            <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                                                                學生未繳交
                                                            </span>
                                                        @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                            <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                學生已繳交
                                                            </span>
                                                        @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                            <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                教師已批改
                                                            </span>
                                                        @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 開放補繳中--}}
                                                    <!-- It should not be happened-->
                                                        @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                    <!-- It should not be happened-->
                                                        @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                                                            <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                等待學生重繳
                                                            </span>
                                                        @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                                                            <span class="badge badge-pill badge-info float-left m-b-5"  style="font-size: 100%;">
                                                                學生已重繳
                                                            </span>
                                                        @endif
                                                    @else {{-- 作業已截止 --}}
                                                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                                                            <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                                                                學生未繳交
                                                            </span>
                                                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                                                            <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                學生已繳交
                                                            </span>
                                                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                                                            <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                                                                教師已批改
                                                            </span>
                                                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                                                            <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                等待學生補繳
                                                            </span>
                                                    {{--<span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">--}}
                                                    {{--催繳--}}
                                                    {{--</span>--}}
                                                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                                                            <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                                                                學生已補繳
                                                            </span>
                                                    @endif
                                                    @endif
                                                </td>
                                                <!-- Third td END-->



                                                <td>{{ $common_courses_name[$i] }}</td>
                                                    <td>{{ $assignments_name[$i] }}</td>

                                                    <td><a class="link" href="{{ route('user.studentDetail', ['student_id' => $student_ids[$i]]) }}">{{ $student_names[$i] }}</a></td>
                                                <td>{{ $student_ids[$i] }}</td>

                                                    <td>
                                                        {!! $titles[$i] !!}
                                                    </td>

                                                <td id="score" style="color:@if($scores[$i] < 60) red @else blue @endif; font-size: 18px;">
                                                    {{ $scores[$i] }}
                                                </td>

                                                <td id="comment" style="color:black; font-size: 18px;">
                                                    {!! $comments[$i] !!}
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
                                        <div class="modal-content" style="width: 400px">
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
                                                    <div class="form-group col-md-6">
                                                        <label>分數</label>
                                                        <input type="number" step="0.01" id="modal_score" name="score" class="form-control"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>教師評語</label>
                                                        <textarea cols="40"id="modal_comment" rows="5" name="comment" class="form-control"></textarea>
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

        var form = '#correct_form';
        var form2 = '#openHandInAssignment';

        $("#correctModal").on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var student_assignment_id = button.data('student-assignment-id');
            var student_assignment = button.parent().parent().parent();

            var score = student_assignment.children('#score').text().trim();
            var comment = student_assignment.children('#comment').text().trim();

            $('#student_assignment_id').val(student_assignment_id);
            $('#modal_score').val(score);
            $('#modal_comment').val(comment);

            if (document.getElementById(button)){
                document.getElementById(button).innerText = "請重新整理頁面"
            }

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

                            button.children("#bold_recorrect").html('<span style="color:red">批改失敗!</span>')
                        }
                        else
                        {
                            $('#form_output').html(data.success);
                            button.children("#bold_recorrect").html('<span style="color:green">批改成功!</span>');
                            button.closest('td').siblings('#score').html('<span style="color:gray; font-size: 20px;">'+data.score+'</span>')
                            button.closest('td').siblings('#comment').html('<span style="color:gray; font-size: 20px;">'+data.comment+'</span>')
                        }
                    }
                })
            });
        });


        $(form2).off().on('submit', function(event){
            event.preventDefault();
            var myform = $(event.relatedTarget);
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
                        myform.children("#bold_rehandIn").html('<span style="color:green">開放成功!</span>');
                        windows.reload();
                    }
                }
            })
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
                    title: '作業批改',
                    text: '複製批改內容',
                    exportOptions: {
                        columns: ':visible'
                    },
                },
                {
                    extend: 'excelHtml5',
                    title: '作業批改',
                    filename: '作業批改',
                    text: '匯出 EXCEL',
                    bom : true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        //modify text in [A1]
                        $('c[r=A1] t', sheet).text( '作業批改' );
                    }
                },
                {
                    extend: 'csv',
                    title: '作業批改',
                    filename: '作業批改',
                    text: '匯出 csv',
                    exportOptions: {
                        columns: ':visible'
                    },
                    bom: true
                },
                {
                    extend: 'print',
                    title: '作業批改',
                    filename: '作業批改',
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
                { "width": "5%", "targets": 1 },
                { "width": "5%", "targets": 2 },
                { "width": "5%", "targets": 3 },
                { "width": "5%", "targets": 4 },
                { "width": "5%", "targets": 5 },
                { "width": "5%", "targets": 6 },
                { "width": "10%", "targets": 7 },
                { "width": "10%", "targets": 8 },
                { "width": "10%", "targets": 9 },
                { "width": "10%", "targets": 10 },
                { "width": "10%", "targets": 11 }

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
