<div class="table-responsive">
    <table id="zero_config" class="table table-striped table-bordered display" style="width:100%">
        <thead>
        <tr>
            <th>動作</th>
            <th>作業狀態</th>
            <th>學生繳交狀態</th>
            <th>共同課程</th>
            <th>作業名稱</th>
            <th>姓名</th>
            <th>學號</th>
            <th>主題</th>
            <th>分數</th>
            <th>評語</th>
            <th>附檔</th>
            <th>學生上傳時間</th>
        </tr>
        </thead>
        <tbody>
        @for($i=0; $i< count($student_assignments_id); $i++)
            <tr>
                <!-- First td START-->
                <td>
                    @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                    @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                    <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                        <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改 </b>
                    </button>
                    @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                    <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                        <b id="bold_rehandIn">要求重繳</b>
                    </a>
                    <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                        <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 批改 </b>
                    </button>
                    @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                    <a id="rehandIn" class="btn-href" onclick="return confirm('確定開放重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                        <b id="bold_rehandIn">開放重繳</b>
                    </a>
                    <br>
                    <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                        <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 重新批改 </b>
                    </button>
                    @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 開放補繳中--}}
                <!-- It should not be happened-->
                    @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                <!-- It should not be happened-->
                    @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                    <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                        <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改 </b>
                    </button>
                    @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                    <a id="rehandIn" class="btn-href" onclick="return confirm('確定要求再度重繳作業?')" href="{{ route('assignment.getChangeAssignmentStatus', ['student_assignment_id' => $student_assignments_id[$i], 'status' => 6]) }}">
                        <b id="bold_rehandIn">要求再度重繳</b>
                    </a>
                    <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                        <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 批改 </b>
                    </button>
                    @endif
                    @else {{-- 作業已截止 --}}
                        @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">開放補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改</b>
                        </button>
                        @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">要求補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">批改</b>
                        </button>
                        @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">開放補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">重新批改</b>
                        </button>
                        @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">更改補繳期限</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">直接批改</b>
                        </button>
                        {{--<span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">--}}
                        {{--催繳--}}
                        {{--</span>--}}
                        @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">要求再度補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect">批改</b>
                        </button>
                        @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">要求補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 直接批改 </b>
                        </button>
                        @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                        <button id="rehandIn" class="btn-href" data-student-assignment-id="{{ $student_assignments_id[$i] }}" data-target="#openMakeUpModal" data-toggle="modal" >
                            <b id="bold_rehandIn">要求補繳</b>
                        </button>
                        <button name="add" data-toggle="modal" data-target="#correctModal" type="submit" class="btn-href" style="color:blue" data-student-assignment-id="{{ $student_assignments_id[$i] }}">
                            <i class="fas fa-pencil-alt"></i><b id="bold_recorrect"> 批改 </b>
                        </button>
                        @endif
                    @endif
                </td>
                <!-- First td END-->

                <!-- Second td START-->
                <td>
                    @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                    <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                        進行中
                    </span>
                    @else {{-- 作業已截止 --}}
                    <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                        已截止
                    </span>
                    @endif
                </td>
                <!-- Second td END-->


                <!-- Third td START-->
                <td id="student_assignment_status">
                    @if($assignments_status[$i] == 1) {{-- 作業進行中 --}}
                        @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                        <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                            學生未繳交
                        </span>
                        @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            學生已繳交
                        </span>
                        @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                            教師已批改
                        </span>
                        @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 開放補繳中--}}
                    <!-- It should not be happened-->
                        @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                    <!-- It should not be happened-->
                        @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            等待學生重繳
                        </span>
                        @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                        <span class="badge badge-pill badge-info float-left m-b-5"  style="font-size: 100%;">
                            學生已重繳
                        </span>
                        @endif
                    @else {{-- 作業已截止 --}}
                        @if($student_assignment_status[$i] == 1) {{-- 學生作業狀態為 未繳交--}}
                        <span class="badge badge-pill badge-danger float-left m-b-5"  style="font-size: 100%;">
                            學生未繳交
                        </span>
                        @elseif($student_assignment_status[$i] == 2) {{-- 學生作業狀態為 已繳交--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            學生已繳交
                        </span>
                        @elseif($student_assignment_status[$i] == 3) {{-- 學生作業狀態為 已批改--}}
                        <span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">
                            教師已批改
                        </span>
                        @elseif($student_assignment_status[$i] == 4) {{-- 學生作業狀態為 補繳中--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            等待學生補繳
                        </span>
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            補繳期限: {{ $makeUpDate[$i] }}
                        </span>
                        {{--<span class="badge badge-pill badge-primary float-left m-b-5"  style="font-size: 100%;">--}}
                        {{--催繳--}}
                        {{--</span>--}}
                        @elseif($student_assignment_status[$i] == 5) {{-- 學生作業狀態為 已補繳--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            學生已補繳
                        </span>
                        @elseif($student_assignment_status[$i] == 6) {{-- 學生作業狀態已為 開放重繳中--}}
                        <span class="badge badge-pill badge-secondary float-left m-b-5"  style="font-size: 100%;">
                            等待學生重繳
                        </span>
                        @elseif($student_assignment_status[$i] == 7) {{-- 學生作業狀態為 已重繳--}}
                        <span class="badge badge-pill badge-info float-left m-b-5"  style="font-size: 100%;">
                            學生已重繳
                        </span>
                        @endif
                    @endif
                </td>
                <!-- Third td END-->



                <td>{{ $common_courses_name[$i] }}</td>
                <td>{{ $assignments_name[$i] }}</td>

                <td><a class="link" href="{{ route('user.studentDetail', ['student_id' => $student_ids[$i]]) }}">{{ $student_names[$i] }}</a></td>
                <td>{{ $student_ids[$i] }}</td>

                <td>
                    {!! $titles[$i] !!}
                </td>

                @if($scores[$i] == 0 and is_null($scores[$i]))
                    <td></td>
                @elseif($scores[$i] == 0)
                    <td id="score" style="color:red; font-size: 18px;">
                        {{ $scores[$i] }}
                    </td>
                @elseif($scores[$i] < 60)
                    <td id="score" style="color:red; font-size: 18px;">
                        {{ $scores[$i] }}
                    </td>
                @elseif($scores[$i] >= 60)
                    <td id="score" style="color:blue; font-size: 18px;">
                        {{ $scores[$i] }}
                    </td>
                @endif

                <td id="comment" style="color:black; font-size: 18px;">
                    {!! $comments[$i] !!}
                </td>


                <td>
                    @if(count($file_names[$i]) != 0)
                        @for($k=0; $k< count($file_names[$i]); $k++)
                            {{ $k+1 }}.
                            <a href="{{ route('dropZone.downloadAssignment', ['first' => 'public', 'second'=> $student_ids[$i], 'third' => $student_assignment_assignments_id[$i], 'fourth' => $file_names[$i][$k]]) }}">
                                {{ $file_names[$i][$k] }}
                            </a>
                            <br>
                        @endfor
                    @else
                        無
                    @endif
                </td>
                <td>{{ $updated_at[$i] }}</td>
            </tr>
        @endfor
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </tfoot>
    </table>
</div>
