<?php

namespace App\Http\Controllers;

use App\common_course;
use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{

    //新增共同課程 (get)
    public function getAddCommonCourse(){
        $teachers = DB::table('teachers')->get();

        return view('course.addCommonCourse', ['teachers' => $teachers]);
    }

    //新增共同課程 (post)
    public function postAddCommonCourse(Request $request){

        $common_course = new common_course([
            'name' => $request->input('courseName'),
            'year'=> $request->input('year'),
            'semester' => $request->input('semester'),
            'start_date' => $request->input('courseStart'),
            'end_date' => $request->input('courseEnd'),
        ]);

        $common_course->save();

        return redirect()->back()->with('message', '已成功新增共同課程！');
    }

    //新增課程 (get)
    public function getAddCourse(){
        $teachers = DB::table('teachers')->get();

        $common_courses = DB::table('common_courses')->get();

        $common_courses_name = $common_courses->pluck('name');

        $common_courses_id = $common_courses->pluck('id');

        return view('course.addCourse', [
            'teachers' => $teachers,
            'common_courses_name' => $common_courses_name,
            'common_courses_id' => $common_courses_id,
            ]);
    }

    //新增課程 (post)
    public function postAddCourse(Request $request){
        $teachers = $request->input('courseTeachers');
        $students = $request->input('courseStudents');

        $common_courses_name =  $request->input('common_courses_name');

        $common_courses_id = DB::table('common_courses')
            ->where('name', $common_courses_name)
            ->value('id');

        //after save, it should be the last id inserted
        $course_id = DB::table('courses')
            ->insertGetId([
                'common_courses_id' => $common_courses_id, 'name' => $request->input('courseName'), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );

        //save courses_id to teacher table
        for ($i=0; $i<count($teachers); $i++){

            //add to teacher_course
            $teacher_id = DB::table('teachers')->where('users_name', '=', $teachers[$i])->value('users_id');
            DB::table('teacher_course')
                ->insert(
                    ['teachers_id' => $teacher_id, 'courses_id' => $course_id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                );
        }

        //save courses_id to student table
        for ($k=0; $k<count($students); $k++){

            //add to student_course
            DB::table('student_course')
                ->insert([
                    ['students_id' => (int)$students[$k],'courses_id' => $course_id]
                ]);
        }
        return redirect()->back()->with('message', '已成功新增課程！');
    }

    //所有共同課程列表 (get) with Datatables
    public function getAllCommonCourses(){
        return view('course.showAllCommonCourses');
    }

    //所有課程列表 (get) with Datatables
    public function getAllCourses(){
        return view('course.showAllCourses');
    }

    public function getShowCourses_Teacher(){
        $teacher_id = Auth::user()->id;

        //找出這個老師的課程
        $teacher_courses = DB::table('teacher_course')
            ->where('teachers_id', $teacher_id)
            ->get();

        $teacher_courses_id = $teacher_courses->pluck('courses_id');

        $courses = DB::table('courses')
            ->whereIn('id', $teacher_courses_id)
            ->get();

        $courses_id = $courses->pluck('id');

        $common_courses_id = $courses->pluck('common_courses_id');

        //進行中的課程
        $common_courses_processing = DB::table('common_courses')
            ->whereIn('id', $common_courses_id)
            ->where('status', 1)
            ->get();


        //取得共同課程的id
        $common_courses_processing_id = $common_courses_processing->pluck('id');

        //取得進行中的課程id
        $courses_processing_id = array();
        for($i=0; $i<count($common_courses_processing); $i++){
            $course_id = DB::table('courses')
                ->where('id', $courses_id[$i])
                ->where('common_courses_id', $common_courses_processing_id[$i])
                ->value('id');

            array_push($courses_processing_id, $course_id);
        }


        //基本資料
        $common_courses_processing_year = $common_courses_processing->pluck('year');
        $common_courses_processing_semester = $common_courses_processing->pluck('semester');
        $common_courses_processing_name = $common_courses_processing->pluck('name');
        $common_courses_processing_start_date = $common_courses_processing->pluck('start_date');
        $common_courses_processing_end_date = $common_courses_processing->pluck('end_date');

        //取得這個課程的指導老師
        $courses_processing_teachers = array();

        for($i=0; $i<count($courses_processing_id); $i++){
            $teacher_course = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();

            $teachers_id = $teacher_course->pluck('teachers_id');

            $teachers_name = array();

            for($k=0; $k<count($teachers_id); $k++){
                $teacher_name = DB::table('users')
                    ->where('id', $teachers_id[$k])
                    ->value('name');

                array_push($teachers_name, $teacher_name);
            }

            array_push($courses_processing_teachers, $teachers_name);
        }

        //課程名稱
        $courses_processing_name = DB::table('courses')
            ->whereIn('id', $courses_processing_id)
            ->pluck('name');

        //已結束的課程
        $common_courses_finished = DB::table('common_courses')
            ->whereIn('id', $common_courses_id)
            ->where('status', 0)
            ->get();

        //取得共同課程的id
        $common_courses_finished_id = $common_courses_finished->pluck('id');

        //取得已結束的課程id
        $courses_finished_id = array();
        for($i=0; $i<count($common_courses_finished); $i++){
            $course_id = DB::table('courses')
                ->where('id', $courses_id[$i])
                ->where('common_courses_id', $common_courses_finished_id[$i])
                ->value('id');

            array_push($courses_finished_id, $course_id);
        }
        //基本資料
        $common_courses_finished_year = $common_courses_finished->pluck('year');
        $common_courses_finished_semester = $common_courses_finished->pluck('semester');
        $common_courses_finished_name = $common_courses_finished->pluck('name');
        $common_courses_finished_start_date = $common_courses_finished->pluck('start_date');
        $common_courses_finished_end_date = $common_courses_finished->pluck('end_date');

        //取得這個課程的指導老師
        $courses_finished_teachers = array();

        for($i=0; $i<count($courses_finished_id); $i++){
            $teacher_course = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();

            $teachers_id = $teacher_course->pluck('teachers_id');

            $teachers_name = array();

            for($k=0; $k<count($teachers_id); $k++){
                $teacher_name = DB::table('users')
                    ->where('id', $teachers_id[$k])
                    ->value('name');

                array_push($teachers_name, $teacher_name);
            }

            array_push($courses_finished_teachers, $teachers_name);
        }

        //課程名稱
        $courses_finished_name = DB::table('courses')
            ->whereIn('id', $courses_processing_id)
            ->pluck('name');

        //hash common course id
        for ($k=0; $k<count($common_courses_processing_id); $k++){
            $hashids = new Hashids('common_courses_id', 5);
            $common_courses_processing_id[$k] = $hashids->encode($common_courses_processing_id[$k]);
        }

        for ($k=0; $k<count($common_courses_finished_id); $k++){
            $hashids = new Hashids('common_courses_id', 5);
            $common_courses_finished_id[$k] = $hashids->encode($common_courses_finished_id[$k]);
        }

        //hash course id
        for($i=0; $i<count($common_courses_processing); $i++){

            $hashids = new Hashids('courses_id', 6);
            $hashed_course_id = $hashids->encode($courses_processing_id[$i]);

            $courses_processing_id[$i] = $hashed_course_id;
        }

        for($i=0; $i<count($common_courses_finished); $i++){

            $hashids = new Hashids('courses_id', 6);
            $hashed_course_id = $hashids->encode($courses_finished_id[$i]);

            $courses_finished_id[$i] = $hashed_course_id;
        }


        return view('course.showCourses_Teacher', [
            'courses_processing_id' => $courses_processing_id,
            'common_courses_processing_id' => $common_courses_processing_id,
            'common_courses_processing_year' => $common_courses_processing_year,
            'common_courses_processing_semester' => $common_courses_processing_semester,
            'common_courses_processing_name' => $common_courses_processing_name,
            'common_courses_processing_start_date' => $common_courses_processing_start_date,
            'common_courses_processing_end_date' => $common_courses_processing_end_date,
            'courses_processing_teacher' => $courses_processing_teachers,
            'courses_processing_name' => $courses_processing_name,

            'courses_finished_id' => $courses_finished_id,
            'common_courses_finished_id' => $common_courses_finished_id,
            'common_courses_finished_year' => $common_courses_finished_year,
            'common_courses_finished_semester' => $common_courses_finished_semester,
            'common_courses_finished_name' => $common_courses_finished_name,
            'common_courses_finished_start_date' => $common_courses_finished_start_date,
            'common_courses_finished_end_date' => $common_courses_finished_end_date,
            'courses_finished_teacher' => $courses_finished_teachers,
            'courses_finished_name' => $courses_finished_name,
        ]);
    }

    public function getShowCommonCourses_Teacher(){
        $teacher_id = Auth::user()->id;

        //找出這個老師的課程
        $courses = DB::table('teacher_course')
            ->where('teachers_id', $teacher_id)
            ->get();

        $courses_id = $courses->pluck('courses_id');

        //進行中的課程
        $courses = DB::table('courses')
            ->whereIn('id', $courses_id)
            ->get();

        $common_courses_id = $courses->pluck('common_courses_id');


        $common_courses_processing = DB::table('common_courses')
            ->whereIn('id', $common_courses_id)
            ->where('status', 1)
            ->get();

        //基本資料
        //共同課程的id 要先 hash，之後再 decode
        $common_courses_processing_id = $common_courses_processing->pluck('id');
        for ($k=0; $k<count($common_courses_processing_id); $k++){
            $hashids = new Hashids('common_courses_id', 5);
            $common_courses_processing_id[$k] = $hashids->encode($common_courses_processing_id[$k]);
        }

        $common_courses_processing_year = $common_courses_processing->pluck('year');
        $common_courses_processing_semester = $common_courses_processing->pluck('semester');
        $common_courses_processing_name = $common_courses_processing->pluck('name');
        $common_courses_processing_start_date = $common_courses_processing->pluck('start_date');
        $common_courses_processing_end_date = $common_courses_processing->pluck('end_date');


        //已結束的課程
        $common_courses_finished = DB::table('common_courses')
            ->whereIn('id', $common_courses_id)
            ->where('status', 0)
            ->get();

        //基本資料
        //共同課程的id 要先 hash，之後再 decode

        $common_courses_finished_id = $common_courses_finished->pluck('id');
        for ($k=0; $k<count($common_courses_finished_id); $k++){
            $hashids = new Hashids('common_courses_id', 5);
            $common_courses_finished[$k] = $hashids->encode($common_courses_finished[$k]);
        }

        $common_courses_finished_year = $common_courses_finished->pluck('year');
        $common_courses_finished_semester = $common_courses_finished->pluck('semester');
        $common_courses_finished_name = $common_courses_finished->pluck('name');
        $common_courses_finished_start_date = $common_courses_finished->pluck('start_date');
        $common_courses_finished_end_date = $common_courses_finished->pluck('end_date');


        return view('course.showCommonCourses_Teacher', [
            'common_courses_processing_id' => $common_courses_processing_id,
            'common_courses_processing_year' => $common_courses_processing_year,
            'common_courses_processing_semester' => $common_courses_processing_semester,
            'common_courses_processing_name' => $common_courses_processing_name,
            'common_courses_processing_start_date' => $common_courses_processing_start_date,
            'common_courses_processing_end_date' => $common_courses_processing_end_date,

            'common_courses_finished_id' => $common_courses_finished_id,
            'common_courses_finished_year' => $common_courses_finished_year,
            'common_courses_finished_semester' => $common_courses_finished_semester,
            'common_courses_finished_name' => $common_courses_finished_name,
            'common_courses_finished_start_date' => $common_courses_finished_start_date,
            'common_courses_finished_end_date' => $common_courses_finished_end_date,
        ]);
    }

    public function getShowSingleCourse_Teacher($common_courses_id){
        $teacher_id = Auth::user()->id;

        //把 $common_course_id 解碼
        $encode_common_course_id = new Hashids('common_courses_id', 5);
        $common_courses_id = $encode_common_course_id->decode($common_courses_id);
        $common_courses_id = $common_courses_id[0]; //because decode() return array

        //找出所有課程中，該課程的 共同課程id 等於 $common_course_id 的課程
        $courses = DB::table('courses')
            ->where('common_courses_id', $common_courses_id)
            ->get();

        $all_courses_id = $courses->pluck('id');

        //篩選出這個共同課程中，是此老師開的課程
        $teacher_courses = DB::table('teacher_course')
            ->whereIn('courses_id', $all_courses_id)
            ->where('teachers_id', $teacher_id)
            ->get();

        //取得是此老師開的課程的 課程id
        $courses_id = $teacher_courses->pluck('courses_id');

        //再來取得這個課程的指導老師
        $courses_teachers = array();

        for($i=0; $i<count($courses_id); $i++){
            $teacher_course = DB::table('teacher_course')
                ->where('courses_id', $courses_id[$i])
                ->get();

            $teachers_id = $teacher_course->pluck('teachers_id');

            $teachers_name = array();

            for($k=0; $k<count($teachers_id); $k++){
                $teacher_name = DB::table('users')
                    ->where('id', $teachers_id[$k])
                    ->value('name');

                array_push($teachers_name, $teacher_name);
            }

            array_push($courses_teachers, $teachers_name);
        }

        //課程基本資料
        $courses_name = array();

        for ($k=0; $k<count($courses_id); $k++){
            $course_name = DB::table('courses')
                ->where('id', $courses_id[$k])
                ->value('name');
            array_push($courses_name, $course_name);
        }

        $common_course = DB::table('common_courses')
            ->where('id', $common_courses_id);

        $courses_year = $common_course->value('year');
        $courses_semester = $common_course->value('semester');
        $courses_start_date = $common_course->value('start_date');
        $courses_end_date = $common_course->value('end_date');

        $common_course_name = $common_course->value('name');

        //hash common course id
        $hashids = new Hashids('common_courses_id', 5);
        $common_courses_id = $hashids->encode($common_courses_id);


        //hash course id
        for($i=0; $i<count($common_course); $i++){

            $hashids = new Hashids('courses_id', 6);
            $hashed_course_id = $hashids->encode($courses_id[$i]);

            $courses_id[$i] = $hashed_course_id;
        }


        return view('course.showSingleCourse_Teacher', [
            'common_courses_id' => $common_courses_id,
            'common_course_name' => $common_course_name,
            'courses_id' => $courses_id,
            'courses_teachers' => $courses_teachers,
            'courses_year' => $courses_year,
            'courses_semester' => $courses_semester,
            'courses_name' => $courses_name,
            'courses_start_date' => $courses_start_date,
            'courses_end_date' => $courses_end_date,
        ]);
    }

    //Datatables
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

    //Datatables
    public function getAllCommonCourses_dt(){
        return DataTables::of(common_course::query())
            ->editColumn('updated_at', function(common_course $common_course){
                return $common_course->updated_at->diffForHumans();
            })
            ->make(true);
    }

    public function getAllCourses_dt(){
        return DataTables::of(Course::query())
            ->editColumn('updated_at', function(Course $course){
                return $course->updated_at->diffForHumans();
            })
            ->make(true);
    }

}
