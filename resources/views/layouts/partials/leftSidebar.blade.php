<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">

                <!-- 公告 -->
                @if(Auth::user()->type != 4)
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-book-multiple"></i><span class="hide-menu"> 公告 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0)
                            <li class="sidebar-item"><a href="{{ route('admin.announcement.show') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有系統公告 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('admin.announcement.create') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增系統公告 </span></a></li>
                        @endif
                        @if(Auth::user()->type == 3)
                            <li class="sidebar-item"><a href="{{ route('teacher.announcement.show') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有公告 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('announcement.create') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增公告 </span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- 共同課程 -->
                @if(Auth::user()->type == 0 or Auth::user()->type == 1 or Auth::user()->type == 2)
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-book-multiple"></i><span class="hide-menu"> 共同課程 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                        <li class="sidebar-item"><a href="{{ route('common_course.showAll') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有共同課程 </span></a></li>
                        @endif
                            @if(Auth::user()->type == 3 or Auth::user()->type == 4)
                                <li class="sidebar-item"><a href="{{ route('courses.showCommonCourses') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 我的共同課程 </span></a></li>
                            @endif

                            @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                        <li class="sidebar-item"><a href="{{ route('common_course.add') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增共同課程 </span></a></li>
                            @endif
                    </ul>
                </li>
                @endif

                <!-- 課程 -->
                @if(Auth::user()->type == 0 or Auth::user()->type == 3 or Auth::user()->type == 4)
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-book-multiple"></i><span class="hide-menu"> 課程 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                            <li class="sidebar-item"><a href="{{ route('course.showCourses') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有課程 </span></a></li>
                        @endif

                        @if(Auth::user()->type == 3)
                            <li class="sidebar-item"><a href="{{ route('courses.showCourses_Teacher') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 我的課程 </span></a></li>
                        @endif

                        @if( Auth::user()->type == 4)
                            <li class="sidebar-item"><a href="{{ route('student.showCourses') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 我的課程 </span></a></li>
                        @endif

                        @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                            <li class="sidebar-item"><a href="{{ route('course.addCourse') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增課程 </span></a></li>
                        @endif
                    </ul>
                </li>
                @endif



                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-pencil"></i><span class="hide-menu">作業 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                            <li class="sidebar-item"><a href="{{ route('assignment.showAllAssignments') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有作業 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('assignment.batchCreateAssignments') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 批量新增作業 </span></a></li>
                        @endif
                        @if(Auth::user()->type == 4)
                            <li class="sidebar-item"><a href="{{ route('assignment.showAssignments') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 查看作業 </span></a></li>
                        @endif

                        @if(Auth::user()->type == 3)
                            <li class="sidebar-item"><a href="{{ route('Assignment.createAssignment') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增作業 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('assignment.manageAssignments_Teacher') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 當學期作業管理 </span></a></li>
                        @endif
                    </ul>
                </li>

                @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account"></i><span class="hide-menu">使用者 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{ route('user.getAllStudents') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 學生 </span></a></li>
                        <li class="sidebar-item"><a href="{{ route('user.getAllTeachers') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 教師 </span></a></li>
                        <li class="sidebar-item"><a href="{{ route('user.getAllTAs') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> TA </span></a></li>
                        <li class="sidebar-item"><a href="{{ route('user.getAllSecrets') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 秘書 </span></a></li>
                    </ul>
                </li>
                @endif
                {{--<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="tables.html" aria-expanded="false"><i class="mdi mdi-file-cloud"></i><span class="hide-menu">檔案庫 </span></a></li>--}}

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account-key"></i><span class="hide-menu">帳號 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0 or Auth::user()->type == 1)
                            <li class="sidebar-item"><a href="{{ route('user.createUser') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增帳號 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('user.importUsers') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 匯入功能 </span></a></li>
                        @endif
                        {{--<li class="sidebar-item"><a href="authentication-login.html" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 個人檔案 </span></a></li>--}}
                            <li class="sidebar-item"><a href="{{ route('user.changePassword') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 更改密碼 </span></a></li>
                        <li class="sidebar-item"><a href="{{ route('auth.signOut') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 登出 </span></a></li>
                    </ul>
                </li>

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
