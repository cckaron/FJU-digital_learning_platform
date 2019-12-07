<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\common_course;
use App\Course;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Imports\UsersStudentImport;
use App\Imports\UsersTeacherImport;
use App\Student;
use App\Ta;
use App\Teacher;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

use Exception;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function getCreateUser(){
        return view('user.createUser');
    }

    public function postCreateUser(Request $request){

        // 帳號不可為非 ASCII (中文)
        $validator = Validator::make($request->all(), [
            'account' => [
                'required',
                'unique:users',
                function($attribute, $value, $fail) {
                    if (!mb_detect_encoding($value, 'ASCII', true)) {
                        return $fail('帳號 不可含有非 英文/數字 的字元');
                    }
                },
            ],
            'id' => [
                'required',
                'unique:users',
                function($attribute, $value, $fail) {
                    if (!mb_detect_encoding($value, 'ASCII', true)) {
                        return $fail('學號 不可含有非 英文/數字 的字元');
                    }
                },
            ],
            'userName' => ['required','string'],
            'userEmail' => ['required', 'string'],
            'userPassword' => ['required', 'string'],
            'userType' => ['required'],
        ])->validate();

        $user = new User([
            'account' => $request->input('account'),
            'id' => $request->input('id'),
            'name' => $request->input('userName'),
            'email' => $request->input('userEmail'),
            'password' => bcrypt($request->input('userPassword')),
            'type' => $request->input('userType'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $user->save();

        if ($request->input('userType') == 3){
            DB::table('teachers')
                ->insert([
                    'users_id' => $request->input('id'),
                    'users_name' => $request->input('userName'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        } else if ($request->input('userType') == 4) {
            DB::table('students')
                ->insert([
                        'users_id' => $request->input('id'),
                        'users_name' => $request->input('userName'),
                        'department' => $request->input('studentDepartment'),
                        'grade' => $request->input('studentGrade'),
                        'class' => $request->input('studentClass'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                ]);
        } else if ($request->input('userType') == 2){
            DB::table('tas')
                ->insert([
                    'users_id' => $request->input('id'),
                    'users_name' => $request->input('userName'),
                    'department' => $request->input('studentDepartment'),
                    'grade' => $request->input('studentGrade'),
                    'class' => $request->input('studentClass'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }


        return redirect()->back()->with('message', '已成功新增帳號！');
    }

    public function getChangePassword(){
        $user_id = Auth::user()->id;

        $user = Auth::user();

        $student = null;
        if ($user->type == 4){
            $student = $user->student()->first();
        }

        return view('user.changePassword', [
            'user_id' => $user_id,
            'user' => $user,
            'student' => $student,
        ]);
    }

    public function postChangePassword(Request $request){
        $user = Auth::user();
        $user_id = (string)$user->id;
        $user_type = $user->type;

        if ($user_type == '3') { //teacher
            $user_id = sprintf("%06d", $user_id);
        }

        Validator::make($request->all(), [
            "email" => ['required', Rule::unique('users')->ignore($user_id)],
            "phone" => ['required', Rule::unique('users')->ignore($user_id)],
            "password"    => [
                function($attribute, $value, $fail) {
                    if ($value[0] != $value[1]){
                        return $fail('新密碼 與 確認新密碼 不相同');
                    }
                },
            ],
            "password.*"  => [
                function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        return $fail('新密碼 不能和 舊密碼 相同');
                    }
                }
            ]
        ])->validate();

        $password = $request->get('password');
        $password = $password[0];
        $email = $request->get('email');
        $phone = $request->get('phone');
        $occupation = $request->get('occupation');

        if ($user->type == '3'){ //teacher
            DB::table('users')
                ->where('id', sprintf("%06d", $user_id))
                ->update([
                    'password' => bcrypt($password),
                    'email' => $email,
                    'phone' => $phone,
                ]);

            DB::table('teachers')
                ->where('users_id', $user_id) //自動補0
                ->update([
                    'profileUpdated' => true,
                ]);
        } else if ($user->type == '4'){ //student
            if ($request->get('agreement') != null){
                $agreement = true;
            } else {
                $agreement = false;
            }

            DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'password' => bcrypt($password),
                    'email' => $email,
                    'phone' => $phone,
                ]);

            DB::table('students')
                ->where('users_id', $user_id)
                ->update([
                    'profileUpdated' => true,
                    'agreement' => $agreement,
                    'occupation' => $occupation
                ]);
        } else {
            DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'password' => bcrypt($password),
                    'email' => $email,
                    'phone' => $phone,
                ]);
        }

        return redirect()->back()->with('message', '個人檔案設定完成!');
    }

    public function importUsers(){
        return view('user.importUsers');
    }

    public function uploadStudents(Request $request){
        $id = Auth::user()->id;

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filePath = $id.'/import';

        Storage::disk('public')->putFileAs(
            $filePath, $file, $filename
        );

        $FullFilePath = 'public/'.$filePath.'/'.$filename;

        Excel::import(new UsersStudentImport(), $FullFilePath);
        Excel::import(new StudentsImport(), $FullFilePath);

    }

    public function uploadTeachers(Request $request){
        $id = Auth::user()->id;

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filePath = $id.'/import';

        Storage::disk('public')->putFileAs(
            $filePath, $file, $filename
        );

        $FullFilePath = 'public/'.$filePath.'/'.$filename;

        Excel::import(new UsersTeacherImport(), $FullFilePath);
        Excel::import(new TeachersImport(), $FullFilePath);
    }

    //學生
    public function getAllStudents(){
        $students = Student::with('user')->get();

        return view('student.showAllStudent', [
            'students' => $students,
        ]);
    }

    public function getStudents_Teacher(){
        $user = Auth::user();

        $teacher = Teacher::where('users_id', $user->id)->first();
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

        foreach($courses as $course){
            $students = $course->student()
                ->get();

            foreach($students as $student){
                $student->user = $student->user()
                    ->first();
            }

            $course->students = $students;
        }

        return view('student.showStudent_Teacher', [
            'courses' => $courses,
        ]);
    }

    public function postChangeStudentContent(Request $request){
        $id = $request->get('id');
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', Rule::unique('users')->ignore($id)],
            'department' => 'required',
            'grade' => 'required',
            'class' => 'required',
            'status' => 'required',
        ]);

        $name = $request->get('name');
        $change_id = $request->get('change_id');
        $department = $request->get('department');
        $grade = $request->get('grade');
        $class = $request->get('class');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $status = $request->get('status');
        $remark = $request->get('remark');

        $error_array = array();
        $success_output = '';

        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            DB::table('students')
                ->where('users_id', $id)
                ->update([
                    'department' => $department,
                    'grade' => $grade,
                    'class' => $class,
                    'status' => $status,
                    'remark' => $remark
                ]);

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'id' => $change_id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            $success_output = '<div class="alert alert-success"> 修改成功！ </div>';

        }
        $output = array(
            'error' => $error_array,
            'id' => $change_id,
            'success' => $success_output,
            'name' => $name,
            'department' => $department,
            'grade' => $grade,
            'class' => $class,
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
            'remark' => $remark,
        );
        echo json_encode($output);
    }

    public function deleteStudent($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        File::deleteDirectory(public_path($id));

        return redirect()->back()->with('message', '刪除成功');

    }

    public function getStudentDetail($student_id){
        $student = Student::where('users_id', $student_id)->first();

        $courses = $student
            ->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select(
                'courses.*',
                'common_courses.name as common_course_name',
                'common_courses.start_date',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.status')
            ->orderBy('common_courses.status', 'desc')
            ->orderBy('common_courses.year', 'desc')
            ->orderBy('common_courses.semester', 'desc')
            ->get();

        $teachers = collect();
        $assignments = collect();
        $student_assignments = collect();

        foreach($courses as $course){
            //覆寫以改變型態
            $course = Course::where('id', $course->id)->first();

            //get courses' teacher
            $teacher = $course->teacher()->select('users_name')->get();
            $teachers->push($teacher);

            //get courses' assignments
            $assignment = $course->assignment()->get();
            $assignments->push($assignment);
        }

        //get student's assignments' detail
        foreach($courses as $key=>$course){
            $temp = collect();

            foreach($assignments[$key] as $assignment){
                //覆寫以改變型態
                $assignment = Assignment::where('id', $assignment->id)->first();

                $student_assignment = $assignment->student()->where('users_id', $student_id)->withPivot(['score', 'title'])->get();

                $temp->push($student_assignment);
            }

            $student_assignments->push($temp);
        }


        return view('student.showStudentDetail', [
            'student' => $student,
            'courses' => $courses,
            'teachers' => $teachers,
            'assignments' => $assignments,
            'student_assignments' => $student_assignments,
        ]);
    }

    //教師
    public function getAllTeachers(){
        $teachers = Teacher::all();

        //because teacher_id has "0" at first, like "051266"
        //so using with(relationship) will get null, we need to use all()
        foreach($teachers as $teacher){
            $teacher->user = $teacher->user()->get();
        }

        return view('teacher.showAllTeachers', [
            'teachers' => $teachers,
        ]);
    }

    public function postChangeTeacherContent(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required',
            'email' => 'required',
            'status' => 'required',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $status = $request->get('status');
        $remark = $request->get('remark');

        $error_array = array();
        $success_output = '';

        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            DB::table('teachers')
                ->where('users_id', $id)
                ->update([
                    'status' => $status,
                    'remark' => $remark
                ]);

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            $success_output = '<div class="alert alert-success"> 修改成功！ </div>';

        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
            'remark' => $remark,
        );
        echo json_encode($output);
    }

    public function deleteTeacher($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功');
    }

    //秘書
    public function getAllSecrets(){
        $secrets = DB::table('users')
            ->where('type', 1)
            ->get();

        return view('secret.showAllSecrets', [
            'secrets' => $secrets
        ]);
    }

    public function postChangeSecretContent(Request $request){
        $id = $request->get('id');

        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', Rule::unique('users')->ignore($id)],
        ]);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $change_id = $request->get('change_id');
        $email = $request->get('email');
        $remark = $request->get('remark');

        $error_array = array();
        $success_output = '';


        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'id' => $change_id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'remark' => $remark
                ]);
            $success_output = '<div class="alert alert-success"> 修改成功！ </div>';

        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
            'id' => $change_id,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'remark' => $remark,
        );
        echo json_encode($output);
    }

    public function deleteSecrets($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功');
    }

    //TA
    public function getAllTAs(){
        $tas = Ta::all();

        //取得 ta 已經加選的進行中課程
        foreach($tas as $ta){
            $ta->user = $ta->user()->get();
            $ta->course_id = $ta->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
                ->where('status', 1)
                ->pluck('id');
        }

        //get courses
        $teachers = Teacher::all();

        foreach($teachers as $teacher){
            //取得老師進行中的課程
            $teacher->courses_id = $teacher->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
                ->where('status', 1)
                ->pluck('id');
        }


        return view('ta.showAllTAs', [
            'tas' => $tas,
            'teachers' => $teachers
        ]);
    }

    public function postChangeTAContent(Request $request){
        $id = $request->get('id');
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => ['required', Rule::unique('users')->ignore($id)],
            'email' => ['required', Rule::unique('users')->ignore($id)],
            'department' => 'required',
            'grade' => 'required',
            'class' => 'required',
            'status' => 'required',
        ]);

        $name = $request->get('name');
        $change_id = $request->get('change_id');
        $department = $request->get('department');
        $grade = $request->get('grade');
        $class = $request->get('class');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $status = $request->get('status');
        $remark = $request->get('remark');

        $error_array = array();
        $success_output = '';

        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            DB::table('tas')
                ->where('users_id', $id)
                ->update([
                    'department' => $department,
                    'grade' => $grade,
                    'class' => $class,
                    'status' => $status,
                    'remark' => $remark
                ]);

            DB::table('users')
                ->where('id', $id)
                ->update([
                    'id' => $change_id,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                ]);
            $success_output = '<div class="alert alert-success"> 修改成功！ </div>';

        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
            'id' => $change_id,
            'name' => $name,
            'department' => $department,
            'grade' => $grade,
            'class' => $class,
            'phone' => $phone,
            'email' => $email,
            'status' => $status,
            'remark' => $remark,
        );
        echo json_encode($output);
    }

    public function deleteTAs($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功');
    }

    public function postTAEditCourse(Request $request){
        $validation = Validator::make($request->all(), [
            'courses_id' => 'array',
        ]);

        $courses_id = $request->get('courses_id');
        $ta_id = $request->get('ta_id');

        $error_array = array();
        $success_output = '';

        //取得當學期所有進行中課程
        $inProgress_courses_id = array();

        $teachers = Teacher::all();
        foreach($teachers as $teacher){
            //取得老師進行中的課程
            $_courses_id = $teacher->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
                ->where('status', 1)
                ->pluck('id');

            foreach($_courses_id as $course_id){
                array_push($inProgress_courses_id, $course_id);
            }
        }

        //取得勾選的課程
        $input_courses_id = array();
        if (count($courses_id) > 0) {
            foreach ($courses_id as $course_id) {
                $course_id = substr($course_id, 1, strlen($course_id) - 2);
                $course_id = explode(",", $course_id);
//                Log::info($course_id);

                foreach($course_id as $id){
                    array_push($input_courses_id, $id);
                }
            }
        }

//        Log::info($input_courses_id);

        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            //確認勾選的課程是否在所有進行中的課程內
            foreach($inProgress_courses_id as $inProgress_course_id){

                //有在裡面，留著或新增
                if (in_array($inProgress_course_id, $input_courses_id) ){
                    if (!DB::table('ta_course')
                        ->where('tas_id', $ta_id)->where('courses_id', $inProgress_course_id)->exists())
                    {
                        DB::table('ta_course')
                            ->insert(['tas_id' => $ta_id, 'courses_id' => $inProgress_course_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                    }
                }
                //沒在裡面，刪除
                else {
                    DB::table('ta_course')
                        ->where('tas_id', $ta_id)
                        ->where('courses_id', $inProgress_course_id)
                        ->delete();
                }

            }

            $success_output = '<div class="alert alert-success"> 成功！ </div>';
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
        );

        echo json_encode($output);
    }

    //deprecated()
    public function getAllStudents_dt(){
        return DataTables::of(Student::query())
            ->editColumn('updated_at', function(Student $student){
                return $student->updated_at->diffForHumans();
            })
            ->editColumn('users_name', function(Student $student){
                $route = route('user.studentDetail', ['id' => $student->users_id]);
                return '<a href="'.$route.'" >
                          '.$student->users_name.'</a>';
            })
            ->editColumn('status', function(Student $student){
                $status = $student->status;
                if ($status == 1){
                    return '在學';
                } elseif ($status == 0) {
                    return '休學';
                }
            })
            ->addColumn('motion', function (Student $student) {
                $routeDetail = route('user.studentDetail', ['id' => $student->users_id]);
                $routeDelete = route('user.deleteStudent', ['id' => $student->users_id]);
                return ' <a href="'.$routeDetail.'" class="btn btn-default btn-sm">
                          詳情</a>
                          <a href="'.$routeDelete.'" class="btn btn-danger btn-sm" onclick="return confirm(\'該學生資料將會一併刪除，確定刪除?\')">
                          刪除</a>';
            })
            ->rawColumns(['motion', 'users_name'])
            ->make(true);
    }

    public function getAllTeachers_dt(){
        return DataTables::of(Teacher::query())
            ->editColumn('updated_at', function(Teacher $teacher){
                return $teacher->updated_at->diffForHumans();
            })
            ->editColumn('status', function(Teacher $teacher){
                $status = $teacher->status;
                if ($status == 1){
                    return '在職';
                } elseif ($status == 0) {
                    return '停職';
                }

            })
            ->addColumn('motion', function (Teacher $teacher) {
                $route = route('user.deleteTeacher', ['id' => $teacher->users_id]);
                return '<a href="'.$route.'" class="btn btn-danger btn-sm" onclick="return confirm(\'該教師資料將會一併刪除，確定刪除?\')">
                          刪除</a>';
            })
            ->rawColumns(['motion'])
            ->make(true);
    }

}
