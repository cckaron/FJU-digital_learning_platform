<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-book-multiple"></i><span class="hide-menu"> 我的課程 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0)
                        <li class="sidebar-item"><a href="{{ route('course.showAllCourses') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有課程 </span></a></li>
                        @endif
                        <li class="sidebar-item"><a href="{{ route('course.addCourse') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增課程 </span></a></li>
                    </ul>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-pencil"></i><span class="hide-menu">我的作業 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 3 or Auth::user()->type == 0)
                            <li class="sidebar-item"><a href="{{ route('assignment.showAllAssignments') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 所有作業 </span></a></li>
                        @endif
                        <li class="sidebar-item"><a href="{{ route('assignment.showAssignments') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 我的作業 </span></a></li>
                            <li class="sidebar-item"><a href="{{ route('Assignment.createAssignment') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增作業 </span></a></li>
                    </ul>
                </li>
                {{--<li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link" href="tables.html" aria-expanded="false"><i class="mdi mdi-file-cloud"></i><span class="hide-menu">檔案庫 </span></a></li>--}}

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-account-key"></i><span class="hide-menu">我的帳號 </span></a>
                    <ul aria-expanded="false" class="collapse  first-level">
                        @if(Auth::user()->type == 0)
                        <li class="sidebar-item"><a href="{{ route('user.createUser') }}" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 新增帳號 </span></a></li>
                        @endif
                        <li class="sidebar-item"><a href="authentication-login.html" class="sidebar-link"><i class="mdi mdi-all-inclusive"></i><span class="hide-menu"> 個人檔案 </span></a></li>
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
