@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />

    <!-- DropZone JS-->
    <link href="{{ URL::to('css/dropzone.css') }}" rel="stylesheet" />


    <link href="{{ URL::to('libs/quill/dist/katex.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/monokai-sublime.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />

    <style>
        input.error {
            border: 6px solid orange;
        }

        label.error {
            font-weight: bold;
            color: red;
            font-size: 20px;
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

        @include('layouts.partials.pageBreadCrumb', ['title' => '作業詳情'])

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

                    <!-- Return Success Message -->
                        <div class="col-md-8 successMessage" hidden>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">提示</h5>

                                    <div class="alert alert-success" role="alert">
                                        繳交成功！
                                    </div>

                                </div>
                            </div>
                        </div>

                </div>


                <!--route的 get 和 post 的網址要一樣，所以post也需要 course_id 和 assignment_id，但沒那麼重要所以就隨機生成 str_random()，不必特別取得正確的 id -->
                <form action="{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}" id="handInAssignment" method="post" class="form-horizontal" enctype="multipart/form-data">

                    <!-- editor -->
                    <div class="row">

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">作業內容</h4>
                                    <!-- Create the editor container -->
                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">名稱</label>
                                        <div class="col-md-3">
                                            <span>{{ $assignment->name }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">作業狀態</label>
                                        <div class="col-md-3">
                                            <span>
                                                @switch($assignment->status)
                                                    @case(1) <!-- 進行中 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            進行中
                                                        </span>
                                                    @break
                                                    @case(0) <!-- 已截止 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            已截止
                                                        </span>
                                                    @break
                                                @endswitch
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">繳交狀態</label>
                                        <div class="col-md-3">
                                            @if($assignment->status == 1)
                                                @switch($student_assignment_status)
                                                    @case(1) <!-- 未繳交 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            未繳交
                                                        </span>
                                                    @break
                                                    @case(2) <!-- 已繳交 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            已繳交
                                                        </span>
                                                    @break
                                                    @case(3) <!-- 教師已批改 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            教師已批改
                                                        </span>
                                                    @break
                                                    @case(4) <!-- 未補繳 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            補繳中
                                                        </span>
                                                    @break
                                                    @case(5) <!-- 已補繳 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%; ">
                                                            已補繳作業
                                                        </span>
                                                    @break
                                                    @case(6) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            尚未重繳
                                                        </span>
                                                    @break
                                                    @case(7) <!-- 已重繳 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            已重繳作業
                                                        </span>
                                                    @break
                                                @endswitch
                                            @else
                                                @switch($student_assignment_status)
                                                    @case(1) <!-- 未繳交 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            未繳交
                                                        </span>
                                                    @break
                                                    @case(2) <!-- 已繳交 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            已繳交
                                                        </span>
                                                    @break
                                                    @case(3) <!-- 教師已批改 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            教師已批改
                                                        </span>
                                                    @break
                                                    @case(4) <!-- 未補繳 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            未補繳
                                                        </span>
                                                    @break
                                                    @case(5) <!-- 已補繳 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            已補繳 (尚未批改)
                                                        </span>
                                                    @break
                                                    @case(6) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-danger" style="font-size: 100%;">
                                                            未重繳
                                                        </span>
                                                    @break
                                                    @case(7) <!-- 未重繳 -->
                                                        <span class="badge badge-pill badge-primary" style="font-size: 100%;">
                                                            已重繳 (尚未批改)
                                                        </span>
                                                    @break
                                                @endswitch
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">開放繳交日期</label>
                                        <div class="col-md-3">
                                            <span>{{ $assignment->start_date }} {{ $assignment->start_time }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">截止日期</label>
                                        <div class="col-md-3">
                                            <span>{{ $assignment->end_date }} {{ $assignment->end_time }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row m-t-20">
                                        @if($assignment->status == 0)
                                            <label class="col-md-2" style="color:red">教師開放補繳日期</label>
                                            @if($student_assignment->makeUpDate != null)
                                                <div class="col-md-3">
                                                    <span>{{ $student_assignment->makeUpDate }}</span>
                                                </div>
                                            @else
                                                <div class="col-md-3">
                                                    <span>未開放</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">作業內容</label>
                                        <div class="col-md-3">
                                            <span>{!! $assignment->content !!}</span>
                                        </div>
                                    </div>

                                    <!-- 分數 -->
                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2">分數</label>
                                        @if($assignment->announce_score == 1)
                                            <div class="col-md-5">
                                                @if($score === 0)
                                                    <span style="color:red;font-size: 20px;">0 分</span>
                                                @elseif($score === null)
                                                    <span style="font-size: 20px;">未評分</span>
                                                @elseif ($score >= 60)
                                                    <span style="color:blue; font-size: 20px;" >
                                                    {{ $score }}
                                                </span> 分
                                                @elseif($score < 60)
                                                    <span style="color:red; font-size: 20px;">
                                                        {{ $score }} 分
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-md-5">
                                                <span>未公開</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- 評語 -->
                                    <div class="form-group row m-t-20">
                                        <label class="col-md-2 m-t-5">教師評語</label>
                                        <div class="col-md-5">
                                            @if($comment != null)
                                                <h4>{!! $comment !!}</h4>
                                            @endif
                                        </div>
                                    </div>

                                </div>


                            </div>
                        </div>

                        <div class="col-md-6"></div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">
                                    @if($assignment->status == 1)
                                            @switch($student_assignment_status)
                                                @case(1) <!-- 未繳交 -->
                                                    繳交作業
                                                @break
                                                @case(2) <!-- 已繳交 -->
                                                    繳交作業
                                                @break
                                                @case(3) <!-- 教師已批改 -->
                                                    我的作業
                                                @break
                                                @case(4) <!-- 未補繳 -->
                                                    補繳作業
                                                @break
                                                @case(5) <!-- 已補繳 -->
                                                    補繳作業
                                                @break
                                                @case(6) <!-- 未重繳 -->
                                                    重繳作業
                                                @break
                                                @case(7) <!-- 未重繳 -->
                                                    重繳作業
                                                @break
                                            @endswitch
                                    @else
                                        @switch($student_assignment_status)
                                            @case(1) <!-- 未繳交 -->
                                                作業詳情
                                            @break
                                            @case(2) <!-- 已繳交 -->
                                                作業詳情
                                            @break
                                            @case(3) <!-- 教師已批改 -->
                                                作業詳情
                                            @break
                                            @case(4) <!-- 未補繳 -->
                                                補繳作業
                                            @break
                                            @case(5) <!-- 已補繳 -->
                                                補繳作業
                                            @break
                                            @case(6) <!-- 未重繳 -->
                                                作業詳情
                                            @break
                                            @case(7) <!-- 未重繳 -->
                                                作業詳情
                                            @break
                                        @endswitch
                                    @endif
                                    </h4>
                                    <!-- Create the editor container -->
                                    <div class="form-group row m-t-30">
                                        <label class="col-md-2 ">學習主題</label>
                                        @if($assignment->status == 1)
                                            @switch($student_assignment_status)
                                                @case(3) <!-- 教師已批改 -->
                                                    <div class="col-md-5">
                                                        <h4>{{ $title }}</h4>
                                                    </div>
                                                @break
                                                @default
                                                    <div class="col-md-5">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="title" name="title" placeholder="請輸入學習主題" value="{!! $title !!}" required>
                                                        </div>
                                                    </div>
                                                @break
                                            @endswitch
                                        @else
                                            @switch($student_assignment_status)
                                                @case(4) <!-- 教師要求補繳 -->
                                                    <div class="col-md-5">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="title" name="title" placeholder="請輸入學習主題" value="{!! $title !!}" required>
                                                        </div>
                                                    </div>
                                                @break
                                                @case(5) <!-- 教師要求補繳 -->
                                                <div class="col-md-5">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="title" name="title" placeholder="請輸入學習主題" value="{!! $title !!}" required>
                                                    </div>
                                                </div>
                                                @break
                                                @default
                                                    <div class="col-md-5">
                                                        <h4>{!! $title !!}</h4>
                                                    </div>
                                                @break
                                            @endswitch
                                        @endif

                                    </div>

                                    <div class="form-group row m-t-30">
                                        <label class="col-md-2" for="announcementContent">附加檔案</label>
                                        <div class="dropzone dropzone-previews col-md-8" id="my_awesome_dropzone">
                                        </div>
                                    </div>

                                    <input hidden name="student_assignment_id" value="{{ $student_assignment_id }}"/>
                                    <input hidden name="assignment_id" value="{{ $assignment_id }}"/>

                                    @if($assignment->status == 1)
                                        @if($student_assignment_status != 3)
                                            <div class="border-top">
                                                <!-- Return Success Message -->
                                                <div class="card-body">
                                                    <input type="submit" class="btn btn-primary" id="btn-sendForm">
                                                    <span class="alert alert-success successMessage m-l-10" style="font-size: 20px" hidden role="alert">
                                                        上傳成功！
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @if($student_assignment_status == 4 or $student_assignment_status == 5)
                                            <div class="border-top">
                                                <!-- Return Success Message -->
                                                <div class="card-body">
                                                    <input type="submit" class="btn btn-primary" id="btn-sendForm">
                                                    <span class="alert alert-success successMessage m-l-10" style="font-size: 20px" hidden role="alert">
                                                        上傳成功！
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
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

    <script src="{{ URL::to('libs/quill/dist/katex.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/highlight.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/quill.min.js') }}"></script>

    <!-- DropZone JS-->
    <script src="{{ URL::to('js/dropzone.js') }}"></script>

    <script src="{{ URL::to('js/jquery.validate.min.js') }}"></script>


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
                        // console.log(value);
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

        $("#editContent").on("submit",function(){
            var myEditor = document.querySelector('#editor');
            var html = myEditor.children[0].innerHTML;

            $("#title").val(html);
        })

    </script>


    <script>
        if({{ $assignment->status }} === 1){  //作業進行中
            if({{ $student_assignment_status }} !== 3){ //教師未批改, 提供上傳修改作業功能
                Dropzone.autoDiscover = false;
                Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳";
                Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
                Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
                Dropzone.prototype.defaultOptions.dictFileTooBig = "檔案超出最大檔案限制: 20MB.";
                Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
                Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
                Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
                Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
                Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";
            } else {
                Dropzone.autoDiscover = false;
                Dropzone.prototype.defaultOptions.dictDefaultMessage = "不允許上傳:教師已完成批改";
            }
        } else { //作業已截止
            if({{ $student_assignment_status }} === 4 || {{ $student_assignment_status }} === 5){ //教師開放補繳 或是 已補繳, 提供上傳修改作業功能
                Dropzone.autoDiscover = false;
                Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳";
                Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
                Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
                Dropzone.prototype.defaultOptions.dictFileTooBig = "檔案超出最大檔案限制: 20MB.";
                Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
                Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
                Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
                Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
                Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";
            } else {
                Dropzone.autoDiscover = false;
                Dropzone.prototype.defaultOptions.dictDefaultMessage = "不允許上傳:作業已截止";
            }
        }

        if({{ $assignment->status }} === 1){  //作業進行中
            if({{ $student_assignment_status }} !== 3){ //教師未批改, 提供上傳修改作業功能
                var mydropZone = new Dropzone("#my_awesome_dropzone", {
                    // The configuration we've talked about above
                    url: '{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}',
                    method: 'POST',
                    autoProcessQueue: false,
                    parallelUploads: 10,
                    maxFilesize: 100,
                    maxFiles: 10,
                    uploadMultiple: true,
                    addRemoveLinks: true,
                    paramName: "file",
                    withCredentials: true,
                    acceptedFiles: '.pdf',

                    init: function () {


                        $("#btn-sendForm").click(function (e) {
                            //validate the data first (in the controller, we can't validate the data and return message
                            //because of using e.preventDefault() and e.stopPropagation();
                            if ($('#handInAssignment').valid()){
                                e.preventDefault();
                                e.stopPropagation();
                                /*
                                (Reference:https://github.com/enyo/dropzone/issues/687)
                                You should check if there are files in the queue. If the queue is empty call directly dropzone.uploadFile().
                                This method requires you to pass in a file. As stated on canisue the File constructor isn't supported on IE/Edge, so just use Blob API, as File API is based on that.
                                The formData.append() method used in dropzone.uploadFile() requires you to pass an object which implements the Blob interface.
                                That's the reason why you cannot pass in a normal object. dropzone version 5.2.0 requires the upload.chunked option
                                 */
                                if (mydropZone.getQueuedFiles().length > 0){
                                    mydropZone.processQueue();
                                } else {
                                    var blob = new Blob();
                                    blob.upload = { 'chunked': mydropZone.defaultOptions.chunking };

                                    mydropZone.uploadFile(blob);
                                }
                            } else {
                                console.log('fail')
                            }
                        });


                        this.on('sending', function(file, xhr, formData) {
                            // Append all form inputs to the formData Dropzone will POST
                            var data = $('#handInAssignment').serializeArray();
                            // console.log(data);
                            $.each(data, function(key, el) {
                                formData.append(el.name, el.value);
                            });
                        });

                        //show the file
                        var filepaths = {!! json_encode($files["filepaths"])  !!};
                        var filenames = {!! json_encode($files["filenames"])  !!};
                        var filesizes = {!! json_encode($files["filesizes"])  !!};

                        for(var i=0; i<filepaths.length; i++){
                            var filepath = filepaths[i];

                            var pathArray = filepath.split('/');


                            //這幾句一定要放在這，不能放在 this.on("complete") 裡面，否則會重複
                            var a = document.createElement('a');

                            a.setAttribute('href', route('dropZone.downloadAssignment', {first: pathArray[0], second: pathArray[1], third:pathArray[2], fourth:pathArray[3]}));
                            a.setAttribute('class',"dz-remove");
                            // a.innerHTML = "下載"+file.previewTemplate.childNodes[12].innerHTML;
                            a.innerHTML = "下載";

                            this.on("complete", function(file){
                                file.previewTemplate.appendChild(a);
                            });

                            let mockFile = { name: filenames[i], size: filesizes[i], dataURL: '{{ URL::to('images/file.png') }}' };

                            if (mockFile.name !== "blob"){
                                let that = this;
                                this.files.push(mockFile);
                                this.emit('addedfile', mockFile);
                                // this.createThumbnailFromUrl(mockFile, mockFile.url);
                                this.createThumbnailFromUrl(mockFile,
                                    this.options.thumbnailWidth,
                                    this.options.thumbnailHeight,
                                    this.options.thumbnailMethod, true, function (thumbnail)
                                    {
                                        that.emit('thumbnail', mockFile, thumbnail);
                                    });
                                this.emit('complete', mockFile);
                                this._updateMaxFilesReachedClass();
                            }

                        }
                    },
                    removedfile: function(file){
                        var filename = file.name;
                        // console.log(filename);
                        $.ajax({
                            url:'{{ route('dropZone.deleteAssignment') }}',
                            method:'POST',
                            data:{
                                'filename': filename,
                                'student_assignment_id': '{{ $student_assignment_id }}',
                            },
                            dataType:'json',
                            success:function(data)
                            {
                                // console.log(data.path);
                            }
                        });
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
                    success: function(){
                        this.options['dictRemoveFile'] = "";
                        $(".successMessage").removeAttr("hidden");
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 3000);
                    }
                });
            } else { //教師已批改, 不允許上傳
                var mydropZone = new Dropzone("#my_awesome_dropzone", {
                    url: '{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}',
                    method: 'POST',
                    init: function () {
                        $("#btn-sendForm").click(function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        });

                        //show the file
                        var filepaths = {!! json_encode($files["filepaths"])  !!};
                        var filenames = {!! json_encode($files["filenames"])  !!};
                        var filesizes = {!! json_encode($files["filesizes"])  !!};

                        for(var i=0; i<filepaths.length; i++){
                            var filepath = filepaths[i];

                            var pathArray = filepath.split('/');


                            //這幾句一定要放在這，不能放在 this.on("complete") 裡面，否則會重複
                            var a = document.createElement('a');

                            a.setAttribute('href', route('dropZone.downloadAssignment', {first: pathArray[0], second: pathArray[1], third:pathArray[2], fourth:pathArray[3]}));
                            a.setAttribute('class',"dz-remove");
                            // a.innerHTML = "下載"+file.previewTemplate.childNodes[12].innerHTML;
                            a.innerHTML = "下載";

                            this.on("complete", function(file){

                                file.previewTemplate.appendChild(a);

                                // file.previewTemplate.removeChild(file.previewTemplate.childNodes[13])
                            });

                            let mockFile = { name: filenames[i], size: filesizes[i], dataURL: '{{ URL::to('images/file.png') }}' };

                            if (mockFile.name !== "blob"){
                                let that = this;
                                this.files.push(mockFile);
                                this.emit('addedfile', mockFile);
                                // this.createThumbnailFromUrl(mockFile, mockFile.url);
                                this.createThumbnailFromUrl(mockFile,
                                    this.options.thumbnailWidth,
                                    this.options.thumbnailHeight,
                                    this.options.thumbnailMethod, true, function (thumbnail)
                                    {
                                        that.emit('thumbnail', mockFile, thumbnail);
                                    });
                                this.emit('complete', mockFile);
                                this._updateMaxFilesReachedClass();
                            }

                        }
                    },
                    accept: function(){
                        alert('不允許上傳：教師已完成批改');
                        location.reload();
                    }
                });
            }
        } else { //作業已截止
            if({{ $student_assignment_status }} === 4 || {{ $student_assignment_status }} === 5){ //教師開放補繳 或是 已補繳, 提供上傳修改作業功能
                var mydropZone = new Dropzone("#my_awesome_dropzone", {
                    // The configuration we've talked about above
                    url: '{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}',
                    method: 'POST',
                    autoProcessQueue: false,
                    parallelUploads: 10,
                    maxFilesize: 100,
                    maxFiles: 10,
                    uploadMultiple: true,
                    addRemoveLinks: true,
                    paramName: "file",
                    withCredentials: true,
                    acceptedFiles: '.pdf',

                    init: function () {
                        $("#btn-sendForm").click(function (e) {
                            //validate the data first (in the controller, we can't validate the data and return message
                            //because of using e.preventDefault() and e.stopPropagation();
                            if ($('#handInAssignment').valid()){
                                e.preventDefault();
                                e.stopPropagation();
                                /*
                                (Reference:https://github.com/enyo/dropzone/issues/687)
                                You should check if there are files in the queue. If the queue is empty call directly dropzone.uploadFile().
                                This method requires you to pass in a file. As stated on canisue the File constructor isn't supported on IE/Edge, so just use Blob API, as File API is based on that.
                                The formData.append() method used in dropzone.uploadFile() requires you to pass an object which implements the Blob interface.
                                That's the reason why you cannot pass in a normal object. dropzone version 5.2.0 requires the upload.chunked option
                                 */
                                if (mydropZone.getQueuedFiles().length > 0){
                                    mydropZone.processQueue();
                                } else {
                                    var blob = new Blob();
                                    blob.upload = { 'chunked': mydropZone.defaultOptions.chunking };

                                    mydropZone.uploadFile(blob);
                                }
                            } else {
                                console.log('fail')
                            }
                        });


                        this.on('sending', function(file, xhr, formData) {
                            // Append all form inputs to the formData Dropzone will POST
                            var data = $('#handInAssignment').serializeArray();
                            // console.log(data);
                            $.each(data, function(key, el) {
                                formData.append(el.name, el.value);
                            });
                        });

                        //show the file
                        var filepaths = {!! json_encode($files["filepaths"])  !!};
                        var filenames = {!! json_encode($files["filenames"])  !!};
                        var filesizes = {!! json_encode($files["filesizes"])  !!};

                        for(var i=0; i<filepaths.length; i++){
                            var filepath = filepaths[i];

                            var pathArray = filepath.split('/');


                            //這幾句一定要放在這，不能放在 this.on("complete") 裡面，否則會重複
                            var a = document.createElement('a');

                            a.setAttribute('href', route('dropZone.downloadAssignment', {first: pathArray[0], second: pathArray[1], third:pathArray[2], fourth:pathArray[3]}));
                            a.setAttribute('class',"dz-remove");
                            // a.innerHTML = "下載"+file.previewTemplate.childNodes[12].innerHTML;
                            a.innerHTML = "下載";

                            this.on("complete", function(file){

                                file.previewTemplate.appendChild(a);

                                // file.previewTemplate.removeChild(file.previewTemplate.childNodes[13])
                            });

                            let mockFile = { name: filenames[i], size: filesizes[i], dataURL: '{{ URL::to('images/file.png') }}' };

                            if (mockFile.name !== "blob"){
                                let that = this;
                                this.files.push(mockFile);
                                this.emit('addedfile', mockFile);
                                // this.createThumbnailFromUrl(mockFile, mockFile.url);
                                this.createThumbnailFromUrl(mockFile,
                                    this.options.thumbnailWidth,
                                    this.options.thumbnailHeight,
                                    this.options.thumbnailMethod, true, function (thumbnail)
                                    {
                                        that.emit('thumbnail', mockFile, thumbnail);
                                    });
                                this.emit('complete', mockFile);
                                this._updateMaxFilesReachedClass();
                            }

                        }
                    },
                    removedfile: function(file){
                        var filename = file.name;
                        // console.log(filename);
                        var announcement_id = $('input[name=announcement_id]').val();
                        $.ajax({
                            url:'{{ route('dropZone.deleteAssignment') }}',
                            method:'POST',
                            data:{
                                'filename': filename,
                                'student_assignment_id': '{{ $student_assignment_id }}',
                            },
                            dataType:'json',
                            success:function(data)
                            {
                                // console.log(data.path);
                            }
                        });
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    },
                    success: function(){
                        this.options['dictRemoveFile'] = "";
                        $(".successMessage").removeAttr("hidden");
                        setTimeout(function(){
                            window.location.reload(1);
                        }, 3000);
                    }
                });
            } else {
                var mydropZone = new Dropzone("#my_awesome_dropzone", {
                    url: '{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}',
                    method: 'POST',
                    init: function () {
                        $("#btn-sendForm").click(function (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        });

                        //show the file
                        var filepaths = {!! json_encode($files["filepaths"])  !!};
                        var filenames = {!! json_encode($files["filenames"])  !!};
                        var filesizes = {!! json_encode($files["filesizes"])  !!};

                        for(var i=0; i<filepaths.length; i++){
                            var filepath = filepaths[i];

                            var pathArray = filepath.split('/');


                            //這幾句一定要放在這，不能放在 this.on("complete") 裡面，否則會重複
                            var a = document.createElement('a');

                            a.setAttribute('href', route('dropZone.downloadAssignment', {first: pathArray[0], second: pathArray[1], third:pathArray[2], fourth:pathArray[3]}));
                            a.setAttribute('class',"dz-remove");
                            // a.innerHTML = "下載"+file.previewTemplate.childNodes[12].innerHTML;
                            a.innerHTML = "下載";

                            this.on("complete", function(file){

                                file.previewTemplate.appendChild(a);

                                // file.previewTemplate.removeChild(file.previewTemplate.childNodes[13])
                            });

                            let mockFile = { name: filenames[i], size: filesizes[i], dataURL: '{{ URL::to('images/file.png') }}' };

                            if (mockFile.name !== "blob"){
                                let that = this;
                                this.files.push(mockFile);
                                this.emit('addedfile', mockFile);
                                // this.createThumbnailFromUrl(mockFile, mockFile.url);
                                this.createThumbnailFromUrl(mockFile,
                                    this.options.thumbnailWidth,
                                    this.options.thumbnailHeight,
                                    this.options.thumbnailMethod, true, function (thumbnail)
                                    {
                                        that.emit('thumbnail', mockFile, thumbnail);
                                    });
                                this.emit('complete', mockFile);
                                this._updateMaxFilesReachedClass();
                            }

                        }
                    },
                    accept: function(){
                        alert('不允許上傳：作業已截止');
                        location.reload();
                    }
                });
            }
        }


    </script>


    <script>
        $('#handInAssignment').validate({
            rules: {
                title: "required",
                student_assignment_id: "required",
                assignment_id: "required",
            },
            messages: {
                title: "請輸入學習主題",
            }
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
