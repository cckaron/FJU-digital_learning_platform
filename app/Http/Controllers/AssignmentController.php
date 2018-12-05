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
        $coursesDetail = collect();
        $teacherDetail = collect();

        $assignments = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->where('status', 1) //進行中的課程
            ->get();

        //該學生進行中的課程，且尚未完成繳交或批改
        for ($i=0; $i<count($assignments); $i++){
            $assignment_id = $assignments[$i]->assignments_id;
            $course_id = DB::table('assignments')
                ->where('id', $assignment_id)
                ->where('status', 1)
                ->value('courses_id');

            // 取得該作業的指導老師，並且加入 $teacherDetail 集合中
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $course_id)
                ->get();
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $course_id)
                ->count();

            $teacher_array = array();
            for ($j=0; $j<$teacher_count; $j++){
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            $teacherDetail->push($teacher_array);

            $course = DB::table('courses')->where('id', $course_id)->get();

            $coursesDetail->push($course);
        }

        return view('assignment.showAssignments', [
            'assignments' => $assignments,
            'coursesDetail'=>$coursesDetail,
            'teacherDetail' => $teacherDetail,
            'count' => count($assignments)
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
