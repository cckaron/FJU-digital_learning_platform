<?php

namespace App\Http\Controllers;

use App\Student;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getCreateUser(){
        return view('user.createUser');
    }

    public function postCreateUser(Request $request){

        $user = new User([
            'account' => $request->input('userAccount'),
            'id' => $request->input('userID'),
            'name' => $request->input('userName'),
            'email' => $request->input('userEmail'),
            'password' => $request->input('userPassword'),
            'type' => $request->input('userType'),
        ]);

        $user->save();


        $student = new Student([
            'users_id' => $request->input('userID'),
        ]);

        $student->save();


        return redirect()->back()->with('message', '已成功新增帳號！');
    }
}
