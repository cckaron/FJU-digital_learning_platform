<?php

namespace App\Http\Controllers;

use App\Teacher;
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

        if ($user->type == 2){
            return $indexController->getTAIndex();
        } else if ($user->type == 3){
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
        } else if ($user->type == 0) {
            $teachers = Teacher::all();
            $hasInProgressCourse = false;

            foreach($teachers as $teacher){
                if($teacher->course()
                    ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                    ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
                    ->where('status', 1)
                    ->exists())
                {
                    return view('dashboard.index', [
                        'hasInProgressCourse' => true,
                        'teacher' => $teacher,
                    ]);
                }
            }
            return view('dashboard.index', [
                'hasInProgressCourse' => $hasInProgressCourse,
            ]);
        }
        return view('dashboard.index');
    }
}
