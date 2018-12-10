<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            //add to teacher_course
            $teacher_id = DB::table('teachers')->where('users_name', '=', $teachers[$i])->value('users_id');


            DB::table('teacher_course')
                ->insert([
                   ['teachers_id' => $teacher_id, 'courses_id' => $course_id]
                ]);
        }

        //save courses_id to student table
        for ($k=0; $k<count($students); $k++){

            DB::table('students')
                ->where('users_id', (int)$students[$k])
                ->update(['courses_id' => $course_id]);

            //save teachers and students' relationship to table
            for ($j=0; $j<count($teachers); $j++){

                //get teacher's id
                $teacher_id = DB::table('teachers')->where('users_name', '=', $teachers[$j])->value('users_id');

                DB::table('student_teacher')
                    ->insert([
                        ['teachers_id' => $teacher_id, 'students_id' => (int)$students[$k]]
                    ]);
            }

            //add to student_course
            DB::table('student_course')
                ->insert([
                    ['students_id' => (int)$students[$k],'courses_id' => $course_id]
                ]);

        }



        return redirect()->back()->with('message', '已成功新增課程！');
    }

    public function getAllCourses(){
        return view('course.showAllCourses');
    }

    public function getShowCourses_Teacher(){
        $teacher_id = Auth::user()->id;

        //找出這個老師的課程
        $courses = DB::table('teacher_course')
            ->where('teachers_id', $teacher_id)
            ->get();

        $courses_id = $courses->pluck('courses_id');

        //進行中的課程
        $courses_processing = DB::table('courses')
            ->whereIn('id', $courses_id)
            ->where('status', 1)
            ->get();

        //基本資料
        $courses_processing_id = $courses_processing->pluck('id');
        $courses_processing_year = $courses_processing->pluck('year');
        $courses_processing_semester = $courses_processing->pluck('semester');
        $courses_processing_name = $courses_processing->pluck('name');
        $courses_processing_start_date = $courses_processing->pluck('start_date');
        $courses_processing_end_date = $courses_processing->pluck('end_date');

        //已結束的課程
        $courses_finished = DB::table('courses')
            ->whereIn('id', $courses_id)
            ->where('status', 0)
            ->get();

        //基本資料
        $courses_finished_id = $courses_finished->pluck('id');
        $courses_finished_year = $courses_finished->pluck('year');
        $courses_finished_semester = $courses_finished->pluck('semester');
        $courses_finished_name = $courses_finished->pluck('name');
        $courses_finished_start_date = $courses_finished->pluck('start_date');
        $courses_finished_end_date = $courses_finished->pluck('end_date');


        return view('course.showCourses_Teacher', [
            'courses_processing_id' => $courses_processing_id,
            'courses_processing_year' => $courses_processing_year,
            'courses_processing_semester' => $courses_processing_semester,
            'courses_processing_name' => $courses_processing_name,
            'courses_processing_start_date' => $courses_processing_start_date,
            'courses_processing_end_date' => $courses_processing_end_date,

            'courses_finished_id' => $courses_finished_id,
            'courses_finished_year' => $courses_finished_year,
            'courses_finished_semester' => $courses_finished_semester,
            'courses_finished_name' => $courses_finished_name,
            'courses_finished_start_date' => $courses_finished_start_date,
            'courses_finished_end_date' => $courses_finished_end_date,
        ]);
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
