<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\student_assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function getCreateAssignment(){
        $course_id = "";
        $course_name = "";

        $teacher_id = Auth::user()->id;


        if (DB::table('teachers')->where('users_id', $teacher_id)->exists()){
            $teacher = DB::table('teachers')->where('users_id', $teacher_id)->first();
            $course_id = $teacher->courses_id;
        }


        if (DB::table('courses')->where('id', $course_id)->exists()){
            $course = DB::table('courses')->where('id', $course_id)->first();
            $course_name = $course->name;
        }

        return view('Assignment.createAssignment', ['course_name' => $course_name]);
    }

    public function postCreateAssignment(Request $request){

        $teacher_id = Auth::user()->id;
        $course = DB::table('teachers')->where('users_id', $teacher_id)->first();
        $course_id = $course->courses_id;

        $assignment = new Assignment([
            'courses_id' => $course_id,
            'name' => $request->input('assignmentName'),
            'start_date' => $request->input('assignmentStart'),
            'end_date' => $request->input('assignmentEnd'),
        ]);

        $assignment->save();

        //get the assignment id which was just saved
        $assignment_id = $assignment->id;


        //create assignment for students
        //create student_course first

        //get the course's student count
        $students = DB::table('students')->where('courses_id', $course_id)->get();

        for ($i=0; $i<count($students); $i++){

            $student = $students[$i];
            $student_id = $student->users_id;

            DB::table('student_assignment')
                ->insert([
                    ['students_id' => $student_id, 'assignments_id' => $assignment_id]
                ]);
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }
}
