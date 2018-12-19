<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Imports\UsersStudentImport;
use App\Imports\UsersTeacherImport;
use App\Student;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use Exception;

class UserController extends Controller
{
    public function getCreateUser(){
        return view('user.createUser');
    }

    public function postCreateUser(Request $request){
        $request->validate([
            'account' => 'required|unique:users',
            'id' => 'required|string|unique:users',
            'userName' => 'required|string',
            'userEmail' => 'required|string',
            'userPassword' => 'required|string',
            'userType' => 'required',
        ]);

        $user = new User([
            'account' => $request->input('account'),
            'id' => $request->input('id'),
            'name' => $request->input('userName'),
            'email' => $request->input('userEmail'),
            'password' => $request->input('userPassword'),
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
                    'users_id' => $request->input('userID'),
                    'users_name' => $request->input('userName'),
                    'grade' => $request->input('studentGrade'),
                    'class' => $request->input('studentClass'),]
                );
        }


        return redirect()->back()->with('message', '已成功新增帳號！');
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
}
