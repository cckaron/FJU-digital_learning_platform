@extends('layouts.Auth')

@section('content')
    <div class="main-wrapper">
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
            <div class="auth-box bg-dark border-top border-secondary">
                <div id="loginform">
                    <h2 style="color: rgba(227,255,165,0.96)">請先更新個人檔案</h2>

                    @if ($message = Session::get('message'))
                        <div class="alert alert-danger" style="margin-top: 10px;">
                            <button type="button" class="close" data-dismiss="alert">x</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li style="font-size: 20px">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form -->
                    <form autocomplete="off" class="form-horizontal m-t-20" id="updateForm" action={{ route('profile.update') }} method="post">
                        <div class="row p-b-30">
                            <div class="col-12">

                                <div class="input-group mb-4 mt-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon2"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input pattern=".{6,}" title="密碼長度需大於 6 個字元" type="password" autocomplete="new-password" id="password" class="form-control form-control-lg" placeholder="新密碼" aria-label="新密碼" aria-describedby="basic-addon1" name="password[]" required="">
                                </div>
                                <div class="input-group mb-4 mt-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white" id="basic-addon2"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" pattern=".{6,}" title="密碼長度需大於 6 個字元" class="form-control form-control-lg" id="confirm_password" placeholder="確認新密碼" aria-label="確認新密碼" aria-describedby="basic-addon2" name="password[]" required="">
                                </div>
                                <h4 style="" id='message'></h4>
                                <div class="input-group mb-4 mt-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-warning text-white" id="basic-addon1"><i class="fas fa-phone-volume"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-lg" placeholder="手機" aria-label="手機" aria-describedby="basic-addon3" name="phone" required="" value="{{ $user->phone }}">
                                </div>
                                <div class="input-group mb-4 mt-4">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-info text-white" id="basic-addon1"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" autocomplete="off" class="form-control form-control-lg" placeholder="信箱" aria-label="信箱" aria-describedby="basic-addon4" name="email" value="{{ $user->email }}" required="">
                                </div>
                                @if($user->type == 4) <!-- 學生 -->
                                    <div class="input-group mb-4 mt-4">
                                        <div class="custom-control custom-checkbox mr-sm-5">
                                            <input type="checkbox" class="custom-control-input" id="agreement" name="agreement" value=1 checked>
                                            <label class="custom-control-label" for="agreement" style="color:white; font-size: 18px">
                                                我已閱讀並同意 <span><a href="https://zh.scribd.com/document/406976852/%E8%BC%94%E4%BB%81%E5%A4%A7%E5%AD%B8%E5%95%86%E7%AE%A1%E5%AD%B8%E7%A8%8B%E7%94%A2%E6%A5%AD%E5%89%B5%E6%96%B0%E7%B3%BB%E5%88%97%E8%AA%B2%E7%A8%8B%E6%88%90%E6%9E%9C%E5%A0%B1%E5%91%8A%E5%85%AC%E9%96%8B%E6%8E%88%E6%AC%8A%E6%9B%B8-20181211" target="_blank" style="color: yellow">授權書</a></span> 中的內容
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row border-top border-secondary">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="p-t-20" style="margin-top: 10px;">
                                        <button class="btn btn-success btn-md float-right" type="submit">確認</button>
                                    </div>
                                </div>
                            </div>
                        </div>




                        {{ csrf_field() }}
                    </form>
                </div>
                <div id="recoverform">
                    <div class="text-center">
                        <span class="text-white">Enter your e-mail address below and we will send you instructions how to recover a password.</span>
                    </div>
                    <div class="row m-t-20">

                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- Login box.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper scss in scafholding.scss -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right Sidebar -->
        <!-- ============================================================== -->
    </div>
@endsection

@section('scripts')
    <!-- ============================================================== -->
    <!-- This page plugin js -->
    <!-- ============================================================== -->
    <script>
        $('[data-toggle="tooltip"]').tooltip();
        $(".preloader").fadeOut();
        // ==============================================================
        // Login and Recover Password
        // ==============================================================
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
        $('#to-login').click(function(){

            $("#recoverform").hide();
            $("#loginform").fadeIn();
        });

        $('#password, #confirm_password').on('keyup', function () {
            if ($('#confirm_password').val() != '') {

                if ($('#password').val() == $('#confirm_password').val()) {
                    $('#message').html('');
                } else
                    $('#message').html('新密碼 與 確認密碼 不一致!').css('color', 'red');
            }

        });
    </script>


@endsection
