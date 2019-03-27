@extends('layouts.main')

@section('css')
    <link href="{{ URL::to('libs/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/jquery-minicolors/jquery.minicolors.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('libs/quill/dist/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ URL::to('css/style.min.css') }}" rel="stylesheet" />

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

        @if($student_assignment_status != 3 )
            @include('layouts.partials.pageBreadCrumb', ['title' => '繳交作業'])
        @else
            @include('layouts.partials.pageBreadCrumb', ['title' => '作業詳情'])
        @endif

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
                                <div class="form-group row">
                                    <h4>
                                        @if($student_assignment_status == 1 or $student_assignment_status == 2)
                                            上傳文件
                                        @elseif($student_assignment_status == 3)
                                            查看文件
                                        @elseif($student_assignment_status == 4)
                                            重繳作業
                                        @elseif($student_assignment_status == 6)
                                            補繳作業
                                        @endif
                                    </h4>
                                </div>

                                <div class="form-group">
                                    <form action="{{ route('dropZone.uploadAssignment') }}" class="dropzone" method="post" enctype="multipart/form-data" id="myDropzone">
                                        <input type="text" name="student_assignment_id" value={{ $student_assignment_id }} hidden/>
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                @if($student_assignment_status != 3)
                <!--route的 get 和 post 的網址要一樣，所以post也需要 course_id 和 assignmen_id，但沒那麼重要所以就隨機生成 str_random()，不必特別取得正確的 id -->
                <form action="{{ route('assignment.handInAssignment', ['course_id'=>str_random(6), 'assignment_id'=>str_random(10)]) }}" id="editContent" method="post" class="form-horizontal">

                    <!-- editor -->
                    <div class="row">

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">主題</h4>
                                    <!-- Create the editor container -->
                                    <div id="editor" style="height: 300px;">
                                        {!! $title !!}
                                    </div>

                                    <textarea id="title" name="title" hidden> {{$title}} </textarea>

                                    <input type="text" name="student_assignment_id" value={{ $student_assignment_id }} hidden/>

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

                @else
                    <!-- score -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">
                                            作業得分：
                                            @if ($score >= 60)
                                            <span style="color:blue">
                                                {{ $score }}
                                            </span> 分
                                            @elseif($score < 60)
                                                <span style="color:red">
                                                {{ $score }}
                                            </span> 分
                                            @endif
                                        </h4>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <!-- comment -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">教師評語：</h4>
                                        <p>{!! $comment !!} </p>
                                </div>
                            </div>

                        </div>
                        </div>
                @endif

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

    <!-- DropZone JS-->
    <script src="{{ URL::to('js/dropzone.js') }}"></script>

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
            theme: 'snow',
        });

        $("#editContent").on("submit",function(){
            var myEditor = document.querySelector('#editor');
            var html = myEditor.children[0].innerHTML;

            $("#title").val(html);
        })

    </script>

    <script>
        if({{ $student_assignment_status }} === 3){
            Dropzone.prototype.defaultOptions.dictDefaultMessage = "已超過繳交期限";
        } else {
            Dropzone.prototype.defaultOptions.dictDefaultMessage = "點此 或 拖曳檔案來上傳";
        }
        Dropzone.prototype.defaultOptions.dictFallbackMessage = "此瀏覽器不支持拖曳檔案的上傳方式";
        Dropzone.prototype.defaultOptions.dictFallbackText = "Please use the fallback form below to upload your files like in the olden days.";
        Dropzone.prototype.defaultOptions.dictFileTooBig = "檔案超出最大檔案限制: 20MB.";
        Dropzone.prototype.defaultOptions.dictInvalidFileType = "上傳的文件格式不正確";
        Dropzone.prototype.defaultOptions.dictCancelUpload = "取消上傳";
        Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "確定取消上傳?";
        Dropzone.prototype.defaultOptions.dictRemoveFile = "刪除檔案";
        Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "已超出檔案數量限制";

        // 未繳交作業 or 已繳交作業 -> 提供上傳
        if({{ $student_assignment_status }} === 1 || {{ $student_assignment_status }} === 2 || {{ $student_assignment_status }} === 4 || {{ $student_assignment_status }} === 6) {
            Dropzone.options.myDropzone = {
                addRemoveLinks: true,
                maxFilesize: 200,
                maxFiles: 10,
                acceptedFiles: ".pdf",
                init: function() {
                    var filepaths = [];
                    var filenames = [];
                    var filesizes = [];

                    //get the assignment's files detail
                    var student_assignment_id = $('input[name=student_assignment_id]').val();
                    $.ajax({
                        url:'{{ route('dropZone.getAssignmentFileDetail') }}',
                        method:'POST',
                        data:{
                            'student_assignment_id': student_assignment_id,
                        },
                        dataType:'json',
                        async: false,
                        success:function(data)
                        {
                            filepaths = data.filepaths;
                            filenames = data.filenames;
                            filesizes = data.filesizes;
                        }
                    });

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

                        var mockFile = { name: filenames[i], size: filesizes[i] };
                        this.files.push(mockFile);
                        this.emit('addedfile', mockFile);
                        this.createThumbnailFromUrl(mockFile, mockFile.url);
                        this.emit('complete', mockFile);
                        this._updateMaxFilesReachedClass();
                    }

                },
                removedfile: function(file){
                    var filename = file.name;
                    console.log(filename);
                    var student_assignment_id = $('input[name=student_assignment_id]').val();
                    $.ajax({
                        url:'{{ route('dropZone.deleteAssignment') }}',
                        method:'POST',
                        data:{
                            'filename': filename,
                            'student_assignment_id': student_assignment_id,
                        },
                        dataType:'json',
                        success:function(data)
                        {
                            //
                        }
                    });
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                },
                success: function(){
                    location.reload();
                }
            };
        }
        else if ({{ $student_assignment_status }} === 3){
            Dropzone.options.myDropzone = {

                init: function() {
                    var filepaths = [];
                    var filenames = [];
                    var filesizes = [];

                    //get the assignment's files detail
                    var student_assignment_id = $('input[name=student_assignment_id]').val();
                    $.ajax({
                        url:'{{ route('dropZone.getAssignmentFileDetail') }}',
                        method:'POST',
                        data:{
                            'student_assignment_id': student_assignment_id,
                        },
                        dataType:'json',
                        async: false,
                        success:function(data)
                        {
                            filepaths = data.filepaths;
                            filenames = data.filenames;
                            filesizes = data.filesizes;
                        }
                    });

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

                        var mockFile = { name: filenames[i], size: filesizes[i] };
                        this.files.push(mockFile);
                        this.emit('addedfile', mockFile);
                        this.createThumbnailFromUrl(mockFile, mockFile.url);
                        this.emit('complete', mockFile);
                        this._updateMaxFilesReachedClass();
                    }

                },
                accept: function(){
                    alert('不允許上傳：作業已截止');
                    location.reload();
                }
            };
        }


    </script>


    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endsection
