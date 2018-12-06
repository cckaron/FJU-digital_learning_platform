<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\student_assignment;
use function Complex\add;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Assign;
use Yajra\DataTables\Facades\DataTables;

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
        $assignment_name = $assignment->name;


        //create assignment for students
        //create student_course first

        //get the course's student count
        $students = DB::table('students')->where('courses_id', $course_id)->get();

        for ($i=0; $i<count($students); $i++){

            $student = $students[$i];
            $student_id = $student->users_id;

            DB::table('student_assignment')
                ->insert([
                    ['students_id' => $student_id, 'assignments_id' => $assignment_id, 'assignments_name' => $assignment_name]
                ]);
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }

    public function getAllAssignments(){
        return view('assignment.showAllAssignments');
    }

    public function getAssignments(){
        $student_id = Auth::user()->id;

        //進行中
        $courses_processing = collect();
        $teachers_processing = collect();

        //已結束
        $courses_finished = collect();
        $teachers_finished = collect();


        // 取得該學生的所有作業
        $assignments = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->get();

        // 取得所有作業的 ID
        $assignments_id = $assignments->pluck('assignments_id');

        // 目的：取得該作業的課程名稱
        // 將作業分類成 1.進行中 2.已結束

        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('id', $assignments_id)
            ->where('status', 1)
            ->get();

        $assignments_processing_name = $assignments_processing->pluck('name');
        $courses_processing_id = $assignments_processing->pluck('courses_id');

        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('id', $assignments_id)
            ->where('status', 0)
            ->get();

        $assignments_finished_name = $assignments_finished->pluck('name');
        $courses_finished_id = $assignments_finished->pluck('courses_id');


        //進行中的作業
        for ($i=0; $i<count($assignments_processing); $i++){

            // 取得進行中作業的指導老師，並且加入 $teachers_processing 集合中
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j=0; $j<$teacher_count; $j++){
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            $teachers_processing->push($teacher_array);

            $course = DB::table('courses')
                ->where('id', $assignments_processing[$i]->courses_id)
                ->get();

            $courses_processing->push($course);
        }

        //已結束的作業
        for ($i=0; $i<count($assignments_finished); $i++){

            // 取得進行中作業的指導老師，並且加入 $teachers_processing 集合中
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j=0; $j<$teacher_count; $j++){
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            $teachers_finished->push($teacher_array);

            $course = DB::table('courses')
                ->where('id', $assignments_finished[$i]->courses_id)
                ->get();

            $courses_finished->push($course);
        }

        return view('assignment.showAssignments', [
            'assignments' => $assignments,

            'assignments_processing' => $assignments_processing,
            'assignments_processing_name' => $assignments_processing_name,
            'courses_processing'=>$courses_processing,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_name' => $assignments_finished_name,
            'courses_finished' => $courses_finished,
            'teachers_finished' => $teachers_finished,
            ]);
    }

    public function getAllAssignments_dt(){
        return DataTables::of(Assignment::query())
            ->editColumn('updated_at', function(Assignment $assignment){
                return $assignment->updated_at->diffForHumans();
            })
            ->make(true);
    }
}
