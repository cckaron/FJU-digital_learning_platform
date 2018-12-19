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
                                            <thead>
                                            <tr>
                                                <th>批改狀態</th>
                                                <th>姓名</th>
                                                <th>學號</th>
                                                <th>分數</th>
                                                <th>學生留言</th>
                                                <th>附檔</th>
                                                <th>上傳時間</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @for($i=0; $i< count($student_ids); $i++)
                                            <tr>
                                                @if($scores[$i] == null)
                                                    <td>
                                                        <input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-success" value="批改"/>
                                                        <input hidden id="student_assignment_id_{{ $i }}" value="{{ $students_assignments_id[$i] }}"/>
                                                    </td>
                                                @else
                                                    <td>
                                                        <h6>已批改</h6>
                                                        <input name="add" id="correct_data{{ $i }}" type="submit" class="btn btn-sm btn-danger" value="修改"/>
                                                        <input hidden id="student_assignment_id_{{ $i }}" value="{{ $students_assignments_id[$i] }}"/>
                                                    </td>
                                                @endif
                                                <td>{{ $student_names[$i] }}</td>
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
                                                        <input type="submit" name="submit" id="action" value="確認" class="btn btn-info">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
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
        $('#zero_config').DataTable();
    </script>

@endsection
