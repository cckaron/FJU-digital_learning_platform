<?php

namespace App\Http\Controllers;

use App\Student;
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
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

        //created for course to use assignment() relationship
        $courses_first = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status', 'common_courses.year as year', 'common_courses.semester as semester')
            ->where('status', 1)
            ->first();

        $students = collect();
        foreach($courses as $course){
            $course_students = $course->student()->get();
            $common_course_name = $course->common_course_name;

            // flatten the students to 1d object
            foreach($course_students as $course_student){

                // to put common_course_name in course_student, should use this method.
                $course_student->common_course_name = $common_course_name;

                $students->push($course_student);

            }
        }

        $student_assignments = collect();
        $assignments = collect();

        foreach($students as $key=> $student){
            $student_assignment = $student->assignment()->withPivot(['score', 'comment'])->orderBy('name')->select('name', 'percentage', 'score', 'comment')->get();

            $accumulated_score = 0;


            foreach ($student_assignment as $key2 => $assignment){

                // get the assignment attribute from student_assignment
                // 僅需取一次值 (在任何一個 $key 取值都可以，因為 每個 student_assignment 的 assignment 必須相同)
                if ($key == 0){
                    //add attribute to temp collect
                    $temp = collect();
                    $temp->name = $assignment->name;
                    $temp->percentage = $assignment->percentage;

                    $assignments->push($temp);
                }


                // 計算加權分數, 累加總分
                $weighted_score = $assignment->score * $assignment->percentage / 100;
                $assignment->weighted_score = $weighted_score;
                $accumulated_score += $weighted_score;

                if ($key2 == count($student_assignment)-1){
                    $assignment->accumulated_score = $accumulated_score;
                }
            }

            $student_assignments->push($student_assignment);

        }

        return view('grade.gradelist', [
            'teacher' => $teacher,
            'course' => $courses_first,
            'assignments' => $assignments,
            'students' => $students,
            'student_assignments' => $student_assignments,
        ]);
    }
}
