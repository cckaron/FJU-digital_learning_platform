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

        @include('layouts.partials.pageBreadCrumb', ['title' => '新增帳號'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('user.createUser') }}" method="post" class="form-horizontal">

                    <!-- editor -->
                    <div class="row">

                        @include('layouts.partials.returnMessage')

                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-3" for="account">帳號</label>
                                        <div class="col-md-9">
                                            <input type="text" id="account" class="form-control" placeholder="帳號" name="account" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3" for="id">學號</label>
                                        <div class="col-md-9">
                                            <input type="text" id="id" class="form-control" placeholder="學號" name="id" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3" for="userName">姓名</label>
                                        <div class="col-md-9">
                                            <input type="text" id="userName" class="form-control" placeholder="姓名" name="userName" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3" for="userEmail">電子信箱</label>
                                        <div class="col-md-9">
                                            <input type="text" id="userEmail" class="form-control" placeholder="電子信箱" name="userEmail" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3" for="userPassword">密碼</label>
                                        <div class="col-md-9">
                                            <input type="text" id="userPassword" class="form-control" placeholder="密碼" name="userPassword" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-15">帳號類型</label>
                                        <div class="col-md-9">
                                            <select id="userType" name="userType" class="select2 form-control custom-select" style="width: 100%; height:36px;">
                                                <option value=4 selected> 學生 </option>
                                                <option value=3 > 教師 </option>
                                                <option value=2 > TA </option>
                                                <option value=1 > 秘書 </option>
                                                <option value=0 > 系統管理員 </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row student">
                                        <label class="col-md-3" for="studentDepartment">學生系級</label>
                                        <div class="col-md-9">
                                            <input type="text" id="studentDepartment" class="form-control" placeholder="例: 商管" name="studentDepartment">
                                        </div>
                                    </div>

                                    <div class="form-group row student">
                                        <label class="col-md-3" for="studentGrade">學生年級</label>
                                        <div class="col-md-9">
                                            <select id="studentGrade" name="studentGrade" class="select2 form-control custom-select" style="width: 100%; height:36px;">
                                                <option value="" selected> 請選擇年級 </option>
                                                <option value='一' > 一 </option>
                                                <option value='二' > 二 </option>
                                                <option value='三' > 三 </option>
                                                <option value='四' > 四 </option>
                                                <option value='五' > 五 </option>
                                                <option value='六' > 六 </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row student">
                                        <label class="col-md-3" for="studentClass">學生班級</label>
                                        <div class="col-md-9">
                                            <select id="studentClass" name="studentClass" class="select2 form-control custom-select" style="width: 100%; height:36px;">
                                                <option value="" selected> 請選擇班級 </option>
                                                <option value='甲' > 甲 </option>
                                                <option value='乙' > 乙 </option>

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

        var student = $('.student');
        var studentDepartment = $('#studentDepartment');
        var studentGrade = $('#studentGrade');
        var studentClass = $('#studentClass');

        studentClass.attr('required',1);
        studentGrade.attr('required',1);

        $('#userType').change(function () {
            var type = $(this).val();
            if (type === '4' || type ==='2'){
                studentDepartment.attr('required', 1);
                studentClass.attr('required',1);
                studentGrade.attr('required',1);
                student.show();
            } else {
                studentDepartment.removeAttr('required');
                studentClass.removeAttr('required');
                studentGrade.removeAttr('required');
                student.hide();
            }
        })

    </script>
@endsection
