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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
                    'users_name' => $request->input('userName')
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
        return view('user.changePassword', [
            'user_id' => $user_id
        ]);
    }

    public function postChangePassword(Request $request){
        $user_id = Auth::user()->id;

        $user = Auth::user();

        $request->validate([
            'newPassword' => ['required', 'min:8', function ($attribute, $value, $fail) use ($user) {
                if (Hash::check($value, $user->password)) {
                    return $fail('新密碼 不能和 舊密碼 相同');
                }
            }],
            'confirmPassword' => 'required|min:8|same:newPassword'
        ]);

        DB::table('users')
            ->where('id', $user_id)
            ->update(['password' => bcrypt($request->input('newPassword'))]);

        return redirect()->back()->with('message', '已成功更改密碼');
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

    public function getAllStudents(){
        $students = Student::with('user')->get();

        return view('student.showAllStudent', [
            'students' => $students,
        ]);
    }

    public function postChangeStudentContent(Request $request){
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required',
            'department' => 'required',
            'grade' => 'required',
            'class' => 'required',
            'status' => 'required',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
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
        $student = Student::where('users_id', $student_id)
            ->first();

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
            ->orderBy('common_courses.status')
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

    public function getAllSecrets(){
        $secrets = DB::table('users')
            ->where('type', 1)
            ->get();

        return view('secret.showAllSecrets', [
            'secrets' => $secrets
        ]);
    }

    public function deleteSecrets($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功');
    }

    public function getAllTAs(){
        $tas = Ta::all();

        //because teacher_id has "0" at first, like "051266"
        //so using with(relationship) will get null, we need to use all()
        foreach($tas as $ta){
            $ta->user = $ta->user()->get();
        }

        return view('ta.showAllTAs', [
            'tas' => $tas,
        ]);
    }

    public function deleteTAs($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '刪除成功');
    }

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
