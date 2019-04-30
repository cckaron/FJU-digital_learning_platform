<!DOCTYPE html>
<html lang="en">
<head>
    <title>系統維護中</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{ URL::to('error-503/images/icons/favicon.ico') }}"/>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/vendor/select2/select2.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/vendor/countdowntime/flipclock.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('error-503/css/main.css') }}">
    <!--===============================================================================================-->
</head>
<body>


<div class="bg-img1 size1 overlay1 p-t-24" style="background-image: url('{{ URL::to('error-503/images/bg01.jpg') }}');">
    <div class="flex-w flex-sb-m p-l-80 p-r-74 p-b-80 respon5">

        <div class="flex-w m-t-10 m-b-10">
            <a href="https://www.facebook.com/FuJenBM/" class="size3 flex-c-m how-social trans-04 m-r-6">
                <i class="fa fa-facebook"></i>
            </a>

            <a href="http://www.bbm.fju.edu.tw" class="size3 flex-c-m how-social trans-04 m-r-6">
                <i class="fa fa-chrome"></i>
            </a>
        </div>
    </div>

    <div class="flex-w flex-sa p-r-200 respon1">
        <div class="p-t-34 p-b-60 respon3">
            <p class="l1-txt1 p-b-10 respon2">
                系統維護中
            </p>

            <h3 class="l1-txt2 p-b-45 respon2 respon4">
                Coming Soon..
            </h3>

            <div class="cd100"></div>

        </div>

    </div>
</div>





<!--===============================================================================================-->
<script src="{{ URL::to('error-503/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ URL::to('error-503/vendor/bootstrap/js/popper.js') }}"></script>
<script src="{{ URL::to('error-503/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ URL::to('error-503/vendor/select2/select2.min.js') }}"></script>
<!--===============================================================================================-->
<script src="{{ URL::to('error-503/vendor/countdowntime/flipclock.js') }}"></script>
<script src="{{ URL::to('error-503/vendor/countdowntime/moment.min.js') }}"></script>
<script src="{{ URL::to('error-503/vendor/countdowntime/moment-timezone.min.js') }}"></script>
<script src="{{ URL::to('error-503/vendor/countdowntime/moment-timezone-with-data.min.js') }}"></script>
<script src="{{ URL::to('error-503/vendor/countdowntime/countdowntime.js') }}"></script>
<script>
    $('.cd100').countdown100({
        /*Set Endtime here*/
        /*Endtime must be > current time*/
        endtimeYear: 2019,
        endtimeMonth: 5,
        endtimeDate: 1,
        endtimeHours: 18,
        endtimeMinutes: 0,
        endtimeSeconds: 0,
        timeZone: "Asia/Taipei"
        // ex:  timeZone: "America/New_York"
        //go to " http://momentjs.com/timezone/ " to get timezone
    });

</script>
<!--===============================================================================================-->
<script src="{{ URL::to('error-503/vendor/tilt/tilt.jquery.min.js') }}"></script>
<script >
    $('.js-tilt').tilt({
        scale: 1.1
    })
</script>
<!--===============================================================================================-->
<script src="{{ URL::to('error-503/js/main.js') }}"></script>

</body>
</html>
