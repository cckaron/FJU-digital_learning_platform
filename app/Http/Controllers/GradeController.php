<?php

namespace App\Http\Controllers;

use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function getGradeList(){
        $teacher = Teacher::where('users_id', Auth::user()->id)->first();
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name')
            ->get();

        //created for course to use assignment() relationship
        $courses_first = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name')
            ->first();

        $assignments = $courses_first->assignment()->get();

        $students = collect();
        foreach($courses as $course){
            $student = $course->student()->get();
            $students->push($student);
        }

        // TODO flatten the students from 2d to 1d 


        foreach($students as $student){
//            $student_assignments = $student->assignment();
        }


        return view('grade.gradelist', [
            'teacher' => $teacher,
            'courses' => $courses,
            'assignments' => $assignments,
            'students' => $students,

        ]);
    }
}
