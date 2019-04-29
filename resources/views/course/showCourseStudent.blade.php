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

        @include('layouts.partials.pageBreadCrumb', ['title' => '課程詳情'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
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

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h5 class="card-title m-t-10" style="padding-right: 20px">公告</h5>
                                </div>

                                <ul class="list-style-none">

                                    @foreach($announcements as $key=>$announcement)
                                        <li class="d-flex no-block card-body @if($key != 0) border-top @endif">
                                            @if($announcement->status == 1) {{-- active --}}
                                            <i class="fa fa-check-circle w-30px m-t-5"></i>
                                            @else {{-- not active --}}
                                            <i class="fa fa-hourglass-end w-30px m-t-5"></i>
                                            @endif

                                            <div>
                                                <div>
                                                    <span style="float: left">
                                                        <a class="link m-b-0 font-medium p-0" data-toggle="collapse" data-parent="#accordian-4" href="#Toggle-{{ $key }}" aria-expanded="false" aria-controls="Toggle-{{ $key }}">
                                                            {{ $announcement->title }}

                                                            <span class="text-active p-l-5" >
                                                                @if($announcement->status == 1)
                                                                    <span class="badge badge-pill badge-primary">已發布</span>
                                                                @else
                                                                    <span class="badge badge-pill badge-dark">未發佈</span>
                                                                @endif
                                                                </span>

                                                            <span class="text-active p-l-5" >
                                                                    @if($announcement->priority == 0)
                                                                        <span class="badge badge-pill badge-danger">置頂</span>
                                                                    @elseif($announcement->priority == 1)
                                                                        <span class="badge badge-pill badge-light">一般</span>
                                                                    @endif
                                                                </span>
                                                        </a>
                                                    </span>

                                                    <span style="float: right; padding: 0px 0px 0px 10px;">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" title="刪除公告" style="padding: 0px 0px 0px 0px;">
                                                                <i class="far fa-trash-alt" style="font-size: 18px"></i>
                                                        </a>
                                                        </span>

                                                </div>

                                                <div class="p-t-5">

                                                    <div id="Toggle-{{ $key }}" class="multi-collapse collapse p-t-10" style="">
                                                        <div class="widget-content">
                                                            <br>
                                                            <h6>
                                                                {!! $announcement->content !!}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="ml-auto">
                                                <div class="text-right">
                                                    <div class="p-t-5">

                                                        {{--<strong><span class="text-muted font-16">@if($course->semester == 1)上@else下@endif學期</span></strong>--}}
                                                        <span style="float:right" class="text-muted m-b-0" style="text-align: center;">{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }} 發佈</span>

                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                    @endforeach
                                </ul>
                                {{ $announcements->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="btn-group">
                                    <h5 class="card-title m-t-10" style="padding-right: 20px">學生清單</h5>
                                    @if(Auth::user()->type == 0)
                                        <button type="button" class="btn btn-default" id="signClassBtn">加選</button>
                                    @endif
                                </div>
                            </div>
                            <table id="zero_config" class="table">
                                <thead>

                                <tr>
                                    <th scope="col">姓名</th>
                                    <th scope="col">學號</th>
                                    <th scope="col">年級</th>
                                    <th scope="col">班級</th>
                                    <th scope="col">動作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($students->chunk(3) as $item)
                                    @foreach($item as $student)
                                        <tr>
                                            <td><a class="link" href="{{ route('user.studentDetail', ['student_id' => $student->users_id]) }}">{{ $student->users_name }}</a></td>
                                            <td class="text-success">{{ $student->users_id }}</td>
                                            <td class="text-success">{{ $student->grade }}</td>
                                            <td class="text-success">{{ $student->class }}</td>
                                            <td>
                                                <a href="{{ route('user.studentDetail', ['student_id' => $student->users_id]) }}" data-toggle="tooltip" data-placement="top" title="查看學生資訊">
                                                    <i class="mdi mdi-account-box"></i>
                                                </a>
                                                @if(Auth::user()->type == 0)
                                                    <a href="{{ route('course.dropCourse', ['courses_id' => $courses_id, 'student_id' => $student->users_id]) }}" data-toggle="tooltip" data-placement="top" title="將此學生退選" onclick="return confirm('該學生於此課程的資料將會一併刪除，確定刪除?')">
                                                        <i class="mdi mdi-close"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Modal -->
                    <div id="signClassModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" id="signClass_form">
                                    <div class="modal-header">
                                        <h4 class="modal-title">學生加選</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{ csrf_field() }}
                                        <span id="form_output"></span>
                                        <div class="form-group">
                                            <label>學號</label>
                                            <input type="text" name="student_number" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" name="button_action" id="button_action" value="插入" />
                                        <input type="hidden" name="courses_id" value="{{ $courses_id }}" />
                                        <input type="submit" name="submit" id="action" value="新增" class="btn btn-info">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                                    </div>
                                </form>
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


    <script>
        $(document).ready(function(){
            $('#signClassBtn').click(function(){
                $('#signClassModal').modal('show');
                $('#signClass_form')[0].reset();
                $('#form_output').html('');
                $('#button_action').val('插入');
                $('#action').val('加簽');
            })

            $('#signClass_form').on('submit', function(event){
                event.preventDefault();
                var form_data = $(this).serialize();
                $.ajax({
                    url: '{{ route('ajax.signClass') }}',
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
                            $('#signClass_form')[0].reset();
                            $('#action').val('加簽');
                            $('.modal-title').text('學生加簽');
                            $('#button_action').val('插入');
                        }
                    }
                })
            })

            $('#signClassModal').on('hidden.bs.modal', function () {
                location.reload();
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
