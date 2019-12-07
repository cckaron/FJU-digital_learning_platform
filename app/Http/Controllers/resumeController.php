<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class resumeController extends Controller
{
    public function preview(){
//        $user = Auth::user();
//        $student = Student::where('users_id', $user->id)->first();

        return view('resume.main', [
//            'student' => $student,
        ]);
    }
}
