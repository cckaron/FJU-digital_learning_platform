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

        @include('layouts.partials.pageBreadCrumb', ['title' => '編輯公告'])

        <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->

                <form action="{{ route('admin.announcement.edit', ['id' => $system_announcement->id]) }}" method="post" id="createAnnouncement" enctype="multipart/form-data">

                    <!-- editor -->
                    <div class="row">

                        <!-- Return Success Message -->
                        <div class="col-md-8" hidden id="successMessage">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">提示</h5>

                                    <div class="alert alert-success" role="alert">
                                        編輯公告成功！
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">

                                    <input hidden name="announcement_id" value="{{ $system_announcement->id }}"/>

                                    <div class="form-group row">
                                        <label class="col-md-3" for="announcementTitle">公告標題</label>
                                        <div class="col-md-9">
                                            <input type="text" id="announcementTitle" class="form-control" placeholder="標題" name="announcementTitle" value="{{ $system_announcement->title }}" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-3 m-t-10" for="announcementContent">公告內容</label>
                                        <div class="col-md-8">
                                            <div id="editor" style="height: 300px;">
                                                {!! $system_announcement->content !!}
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
                                                <input type="checkbox" class="custom-control-input" id="topPost" name="topPost" @if($system_announcement->priority == 0) checked @endif>
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
    <script src="{{ URL::to('js/jquery.timepicker.min.js') }}"></script>
    <!-- DropZone JS-->
    <script src="{{ URL::to('js/dropzone.js') }}"></script>

    <!-- quill editor -->
    <script src="{{ URL::to('libs/quill/dist/katex.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/highlight.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/quill.min.js') }}"></script>
    <script src="{{ URL::to('libs/quill/dist/image-resize.min.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;
        Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
        Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
        Dropzone.prototype.defaultOptions.dictFileTooBig = "超出檔案大小限制(最大: 20MB)";
        Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
        Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
        Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
        Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";
        Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳";

        var mydropZone = new Dropzone("#my_awesome_dropzone", {
            // The configuration we've talked about above
            url: '{{ route('admin.announcement.edit', ['id' => $system_announcement->id]) }}',
            method: 'POST',
            autoProcessQueue: false,
            parallelUploads: 10,
            maxFilesize: 100,
            maxFiles: 10,
            uploadMultiple: true,
            addRemoveLinks: true,
            paramName: "file",
            withCredentials: true,

            init: function () {
                $("#btn-sendForm").click(function (e) {

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
                        var myEditor = document.querySelector('#editor');
                        var html = myEditor.children[0].innerHTML;

                        $("#announcementContent").val(html);

                        var blob = new Blob();
                        blob.upload = { 'chunked': mydropZone.defaultOptions.chunking };

                        mydropZone.uploadFile(blob);
                    }




                });


                this.on('sending', function(file, xhr, formData) {
                    var myEditor = document.querySelector('#editor');
                    var html = myEditor.children[0].innerHTML;

                    $("#announcementContent").val(html);

                    // Append all form inputs to the formData Dropzone will POST
                    var data = $('#createAnnouncement').serializeArray();
                    console.log(data);
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
                console.log(filename);
                var announcement_id = $('input[name=announcement_id]').val();
                $.ajax({
                    url:'{{ route('admin.announcement.deleteAttachment') }}',
                    method:'POST',
                    data:{
                        'filename': filename,
                        'announcement_id': announcement_id,
                    },
                    dataType:'json',
                    success:function(data)
                    {
                        console.log(data.path);
                    }
                });
                var _ref;
                return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
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
    </script>

    <script>
        var toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],

            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction

            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],
            // ['image'],
            ['clean']                                         // remove formatting button
        ];
        var quill = new Quill('#editor', {
            modules: {
                formula: true,
                syntax: true,
                toolbar: toolbarOptions,
                imageResize: {}
            },
            placeholder: '請輸入作業內容..',
            theme: 'snow',
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
