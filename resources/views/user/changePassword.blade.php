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

        @include('layouts.partials.pageBreadCrumb', ['title' => '更改個人檔案'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('user.changePassword') }}" method="post" class="form-horizontal">

                    <!-- editor -->
                    <div class="row">

                        @include('layouts.partials.returnMessage')

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-5" for="password">Email</label>
                                        <div class="col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="text" id="email" class="form-control" placeholder="Email" name="email" value="{{ $user->email }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-5" for="password">聯絡電話</label>
                                        <div class="col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="text" id="phone" class="form-control" placeholder="聯絡電話" name="phone" value="{{ $user->phone }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->type == 4)
                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-5" for="password">職業</label>
                                        <div class="col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="text" id="phone" class="form-control" placeholder="職業" name="occupation" value="{{ $student->occupation }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-5" for="password">新密碼</label>
                                        <div class="col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="password" pattern=".{6,}" title="密碼長度需大於 6 個字元" id="password" class="form-control password" placeholder="新密碼" name="password[]">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-eye-slash toggle-password"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-5" for="confirm_password">確認密碼</label>
                                        <div class="col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="password" pattern=".{6,}" title="密碼長度需大於 6 個字元" id="confirm_password" class="form-control password" placeholder="確認密碼" name="password[]">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-eye-slash toggle-password"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 style="" id='message'></h4>

                                    </div>

                                </div>
                                <div class="border-top">
                                    <div class="card-body">
                                        <input type="submit" class="btn btn-primary" value="儲存">
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

        var student = $('.student');
        var studentGrade = $('#studentGrade');
        var studentClass = $('#studentClass');

        studentClass.attr('required',1);
        studentGrade.attr('required',1);

        $('#userType').change(function () {
            var type = $(this).val();
            if (type === '4'){
                studentClass.attr('required',1);
                studentGrade.attr('required',1);
                student.show();
            } else {
                studentClass.removeAttr('required');
                studentGrade.removeAttr('required');
                student.hide();
            }
        });


        $('#password, #confirm_password').on('keyup', function () {
            if ($('#confirm_password').val() != '') {

                if ($('#password').val() == $('#confirm_password').val()) {
                    $('#message').html('');
                } else
                    $('#message').html('新密碼 與 確認密碼 不一致!').css('color', 'red');
            }

        });

        // toggle password visibility
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $(this).parent().parent().siblings(".password");

            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
@endsection
