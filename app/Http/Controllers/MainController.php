<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function getIndex(){
        $user = Auth::user();

        $indexController = new IndexController();
        $profileController = new ProfileController();

        $user = User::where('id', $user->id)->first();


        if ($user->type == 3){
            $teacher = $user->teacher()->first();

            if ($teacher->profileUpdated){
                return $indexController->getTeacherIndex();
            }
            return $profileController->getUpdateProfile();
        } else if ($user->type == 4){

            $student = $user->student()->first();

            if ($student->profileUpdated){
                return $indexController->getStudentIndex();
            }
            return $profileController->getUpdateProfile();
        }
        return view('dashboard.index');
    }
}
