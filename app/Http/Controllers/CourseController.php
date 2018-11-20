<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    public function getAddCourse(){
        $teachers = DB::table('teachers')->get();

        return view('course.addCourse', ['teachers' => $teachers]);
    }

    public function postAddCourse(Request $request){

        $teachers = $request->input('courseTeachers');
        $students = $request->input('courseStudents');

        $course = new Course([
            'name' => $request->input('courseName'),
            'year'=> $request->input('year'),
            'semester' => $request->input('semester'),
            'start_date' => $request->input('courseStart'),
            'end_date' => $request->input('courseEnd'),
        ]);

        $course->save();

        //after save, it should be the last id inserted
        $course_id = $course->id;

        //save courses_id to teacher table
        for ($i=0; $i<count($teachers); $i++){

            DB::table('teachers')
                ->where('users_name', $teachers[$i])
                ->update(['courses_id' => $course_id]);
        }

        //save courses_id to student table
        for ($k=0; $k<count($students); $k++){

            DB::table('students')
                ->where('users_id', (int)$students[$k])
                ->update(['courses_id' => $course_id]);

            //save teachers and students' relationship to table
            for ($j=0; $j<count($teachers); $j++){

                //get teacher's name
                $teacher = DB::table('teachers')->where('users_name', '=', $teachers[$j])->first();
                $teacher_id = $teacher->users_id;

                DB::table('student_teacher')
                    ->insert([
                        ['teachers_id' => $teacher_id, 'students_id' => (int)$students[$k]]
                    ]);
            }

        }



        return redirect()->back()->with('message', '已成功新增課程！');
    }

    public function getAllCourses(){
        return view('course.showAllCourses');
    }

    public function getUsers_dt(){
        return DataTables::of(Student::query())
            ->editColumn('created_at', function(Student $student){
                return $student->created_at->diffForHumans();
            })
            ->addColumn('checkbox', function (Student $student) {
//                return '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="'.$user->id.'" name="'.$user->id.'" /><label class="custom-control-label" for="'.$user->id.'"></label></div>';
                return '<label class="customcheckbox"><input type="checkbox" class="listCheckbox" name="courseStudents[]" value="'.$student->users_id.'"/><span class="checkmark"></span></label>';
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }

    public function getAllCourses_dt(){
        return DataTables::of(Course::query())
            ->editColumn('updated_at', function(Course $course){
                return $course->updated_at->diffForHumans();
            })
            ->make(true);
    }


    public function whenCourseEnd(){
        //        //save to teacher_course table
//        for ($i = 0;$i < count($teachers); $i++){
//            $teacher = DB::table('users')->where('users_name', '=', $teachers[$i])->first();
//            $teacher_id = $teacher->id;
//
//            $teacher_course = new teacher_course([
//                'teachers_id' => $teacher_id,
//                'courses_id' => $course_id,
//            ]);
//
//            $teacher_course->save();
//        }
//
//        //save to student_course table
//        for ($i = 0;$i < count($teachers); $i++){
//            $teacher = DB::table('users')->where('users_name', '=', $teachers[$i])->first();
//            $teacher_id = $teacher->id;
//
//            $teacher_course = new teacher_course([
//                'teachers_id' => $teacher_id,
//                'courses_id' => $course_id,
//            ]);
//
//            $teacher_course->save();
//        }
    }
}
