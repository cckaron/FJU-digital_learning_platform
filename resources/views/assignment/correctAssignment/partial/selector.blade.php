@if($user->type == 0)
    <div class="row p-b-10">
        <h5 class="p-r-10">當前批改身份：{{ $teacher->users_name }} 教師</h5>
        <select id="teacherSelect" class="select2 form-control custom-select" style="width:20%; height: 30px;">
            <option selected>選擇其他教師</option>
            @foreach($teachers as $teacher)
                <option value="{{ $teacher->users_id }}">{{ $teacher->users_name }}</option>
            @endforeach
        </select>
    </div>
@endif
