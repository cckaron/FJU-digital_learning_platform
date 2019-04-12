<?php

namespace App\Http\Controllers;

use App\common_course;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CommonCourseController extends Controller
{
    //新增共同課程 (get)
    public function getAddCommonCourse(){
        $teachers = DB::table('teachers')->get();

        $today = Carbon::now();
        $year = $today->year;

        //西元年
        $twYear = $year-1911;

        return view('common_course.addCommonCourse', [
            'teachers' => $teachers,
            'year' => $twYear,
        ]);
    }

    //新增共同課程 (post)
    public function postAddCommonCourse(Request $request){
        $request->validate([
            'courseName' => 'required',
            'year' => 'required',
            'semester' => 'required',
            'courseStart' => 'required|date|date-format:Y/m/d|before:courseEnd',
            'courseEnd' => 'required|date|date-format:Y/m/d|after:courseStart',
        ]);

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

    public function deleteCommonCourse($id){
        DB::table('common_courses')
            ->where('id', $id)
            ->delete();

        return redirect()->back()->with('message', '已成功刪除共同課程');
    }

    //所有共同課程列表 (get)
    public function getAllCommonCourses(){
        $common_courses = common_course::all();
        return view('common_course.showAllCommonCourses', [
            'common_courses' => $common_courses
        ]);
    }

    public function getShowCommonCourses(){
        $id = Auth::user()->id;
        $role = Auth::user()->type;

        if ($role == 3){ //如果是老師的話
            //找出這個老師的課程
            $courses = DB::table('teacher_course')
                ->where('teachers_id', $id)
                ->get();
        } else if ($role == 4){ //如果是學生的話
            $courses = DB::table('student_course')
                ->where('students_id', $id)
                ->get();
        } else {
            $courses = null;
        }


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
            $common_courses_finished_id[$k] = $hashids->encode($common_courses_finished_id[$k]);
        }

        return view('course.showCommonCourses', [
            'common_courses_processing' => $common_courses_processing,
            'common_courses_processing_id' => $common_courses_processing_id,

            'common_courses_finished' => $common_courses_finished,
            'common_courses_finished_id' => $common_courses_finished_id,
        ]);
    }
}
