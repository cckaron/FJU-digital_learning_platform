@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/jquery.timepicker.min.css') }}" rel="stylesheet" />

    <!-- DropZone JS-->
    <link href="{{ URL::to('css/dropzone.css') }}" rel="stylesheet" />
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

        @include('layouts.partials.pageBreadCrumb', ['title' => '新增公告'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('admin.announcement.create') }}" method="post" id="createAnnouncement" enctype="multipart/form-data">

                    <!-- editor -->
                    <div class="row">

                        <!-- Return Success Message -->
                        <div class="col-md-8" hidden id="successMessage">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">提示</h5>

                                    <div class="alert alert-success" role="alert">
                                        新增公告成功！
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    {{--{{ $courses }}--}}

                                    <div class="form-group row">
                                        <label class="col-md-3" for="announcementTitle">公告標題</label>
                                        <div class="col-md-9">
                                            <input type="text" id="announcementTitle" class="form-control" placeholder="標題" name="announcementTitle" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-10" for="announcementContent">公告內容</label>
                                        <div class="col-md-8">
                                            <div id="editor" style="height: 300px;">

                                            </div>

                                            <textarea id="announcementContent" name="announcementContent" hidden>  </textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-10" for="announcementContent">附加檔案</label>
                                        <div class="dropzone dropzone-previews col-md-8" id="my_awesome_dropzone">
                                        </div>
                                    </div>
                                    {{--<div id="myAwesomeDropzone" class="dropzone"></div>--}}


                                    <div class="form-group row">
                                        <label class="col-md-3">置頂公告</label>
                                        <div class="col-md-9">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="topPost" name="topPost">
                                                <label class="custom-control-label" for="topPost">置頂</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="border-top">
                                    <div class="card-body">
                                        <input type="submit" class="btn btn-primary" id="btn-sendForm">
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
    <script src="{{ URL::to('js/jquery.timepicker.min.js') }}"></script>
    <!-- DropZone JS-->
    <script src="{{ URL::to('js/dropzone.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;
        Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
        Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
        Dropzone.prototype.defaultOptions.dictFileTooBig = "檔案超出最大檔案限制: 20MB.";
        Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
        Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
        Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
        Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";
        Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳";

        var mydropZone = new Dropzone("#my_awesome_dropzone", {
            // The configuration we've talked about above
            url: '{{ route('admin.announcement.create') }}',
            method: 'POST',
            autoProcessQueue: false,
            parallelUploads: 10,
            uploadMultiple: true,
            addRemoveLinks: true,
            paramName: "file",
            withCredentials: true,

            init: function () {
                $("#btn-sendForm").click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    mydropZone.processQueue();
                });


                this.on('sending', function(file, xhr, formData) {
                    var myEditor = document.querySelector('#editor');
                    var html = myEditor.children[0].innerHTML;

                    $("#announcementContent").val(html);

                    // Append all form inputs to the formData Dropzone will POST
                    var data = $('#createAnnouncement').serializeArray();
                    $.each(data, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                });
            },
            success: function(){
                this.options['dictRemoveFile'] = "";
                $("#successMessage").removeAttr("hidden");
            }
        });

    </script>

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


        /*datepicker*/
        $('#datepicker-start').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",

        });
        $('#datepicker-end').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd",
        });

        $('#timepicker-start').timepicker(
            { 'scrollDefault': 'now',am: '上午', pm: '下午', AM: '上午', PM: '下午', decimal: '.', mins: 'mins', hr: 'hr', hrs: 'hrs' });
        $('#timepicker-end').timepicker(
            { 'scrollDefault': 'now',am: '上午', pm: '下午', AM: '上午', PM: '下午', decimal: '.', mins: 'mins', hr: 'hr', hrs: 'hrs' });

        var quill = new Quill('#editor', {
            theme: 'snow',
        });


    </script>



@endsection
