<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Imports\UsersStudentImport;
use App\Imports\UsersTeacherImport;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
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
                    'grade' => $request->input('studentGrade'),
                    'class' => $request->input('studentClass'),]
                );
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
        return view('student.showAllStudent');
    }

    public function deleteStudent($id){

        DB::table('users')
            ->where('id', $id)
            ->delete();

        File::deleteDirectory(public_path($id));

        return redirect()->back()->with('message', '刪除成功');

    }

    public function getStudentDetail($student_id){
        $student = DB::table('students')
            ->where('users_id', $student_id)
            ->first();



        return view('student.showStudentDetail', [
            'student' => $student
        ]);
    }

    public function getAllTeachers(){
        return view('teacher.showAllTeachers');
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

    public function getAllEmployees(){
        $employees = DB::table('users')
            ->where('type', 2)
            ->get();

        return view('employee.showAllEmployees', [
            'employees' => $employees
        ]);
    }

    public function deleteEmployees($id){

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
