<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\common_course;
use App\student_assignment;
use App\Course;
use App\User;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Hashids\Hashids;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function PHPSTORM_META\type;
use Yajra\DataTables\Facades\DataTables;

class AssignmentController extends Controller
{
    public function getCreateAssignment(){
        $course_id = array();
        $course_names = array();

        $teacher_id = Auth::user()->id;


        // 取得老師的所有課程
        if (DB::table('teacher_course')->where('teachers_id', $teacher_id)->exists()){
            $teacher = DB::table('teacher_course')->where('teachers_id', $teacher_id)->get();
            $course_id = $teacher->pluck('courses_id');
        }

        //取得課程的 共同課程名稱
        $common_courses_id = DB::table('courses')
            ->whereIn('id', $course_id)
            ->pluck('common_courses_id');

        $common_courses_name = DB::table('common_courses')
            ->whereIn('id', $common_courses_id)
            ->pluck('name');

        for ($i=0; $i<count($course_id); $i++){
            if (DB::table('courses')->where('id', $course_id[$i])->exists()){
                $course_name = DB::table('courses')->where('id', $course_id[$i])->value('name');
                array_push($course_names, $course_name);
            }
        }


        return view('Assignment.createAssignment', [
            'course_names' => $course_names,
            'common_courses_name' => $common_courses_name
            ]);
    }

    public function postCreateAssignment(Request $request){
        $request->validate([
            'assignmentName' => 'required',
            'assignmentStart' => 'required|date|date-format:Y/m/d|before:assignmentEnd',
            'assignmentEnd' => 'required|date|date-format:Y/m/d|after:assignmentStart',
            'assignmentStartTime' => 'required',
            'assignmentEndTime' => 'required',
            'courseName' => 'required'
        ]);

        $course_name = $request->input('courseName');

        $course_id = DB::table('courses')
            ->where('name', $course_name)
            ->value('id');

        //取得該課程所有授課教師
        $teachers = DB::table('teacher_course')
            ->where('courses_id', $course_id)
            ->get();
        $teachers_id = $teachers->pluck('teachers_id');

        $start_time = date("H:i", strtotime($request->input('assignmentStartTime')));
        $end_time = date("H:i", strtotime($request->input('assignmentEndTime')));

        //新增作業
        $assignment = new Assignment([
            'name' => $request->input('assignmentName'),
            'start_date' => $request->input('assignmentStart'),
            'start_time' => $start_time,
            'end_date' => $request->input('assignmentEnd'),
            'end_time' => $end_time,
            'courses_id' => $course_id,
        ]);

        $assignment->save();

        //取得剛剛新增的作業id
        $assignment_id = $assignment->id;

        for($i=0; $i<count($teachers); $i++){
            DB::table('teacher_assignment')
                ->insert(
                    ['teachers_id' => $teachers_id[$i], 'assignments_id' => $assignment_id]
                );
        }

        //create assignment for students
        //create student_course first

        //get the course's student count
        $students = DB::table('student_course')->where('courses_id', $course_id)->get();
        $students_id = $students->pluck('students_id');

        for ($i=0; $i<count($students); $i++){

            DB::table('student_assignment')
                ->insert([
                    ['students_id' => $students_id[$i], 'assignments_id' => $assignment_id]
                ]);

            Storage::makeDirectory('public/'.$students_id[$i].'/'.$assignment_id);
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }

    public function deleteAssignment($id){
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $assignment_id = $encode_assignment_id->decode($id);

        DB::table('assignments')
            ->where('id', $assignment_id)
            ->delete();

        return redirect()->back()->with('message', '刪除作業成功！');
    }

    public function getBatchCreateAssignments(){
        //確認該 one 是否存在 many
        $common_courses = common_course::with('course')->get();

        //把不含 course 的 common course 刪掉（過濾）
        for ($i=0; $i<= count($common_courses); $i++){
            if (! $common_courses[$i]->course()->exists()){
                $common_courses->forget($i);
            }
        }


        $teachers_name = collect();
        //把課程的老師塞進去 collection 裡面
        for ($i=0; $i < count($common_courses); $i++){
            for ($k=0; $k < count($common_courses[$i]->course); $k++){
                $course_id = $common_courses[$i]->course[$k]->id;
                $teachers_id = DB::table('teacher_course')
                    ->where('courses_id', $course_id)
                    ->pluck('teachers_id');

                $teachers_name_temp = array();
                foreach($teachers_id as $teacher_id){
                    $teacher_name = User::find($teacher_id)->name;
                    array_push($teachers_name_temp, $teacher_name);
                }

                $teachers_name->push($teachers_name_temp);
            }
        }

        $test = $teachers_name;

        return view('assignment.batchCreateAssignments', [
            'common_courses' => $common_courses,
            'teachers_name' => $teachers_name
        ]);
    }

    public function postBatchCreateAssignments(Request $request){
        $request->validate([
            'assignmentName' => 'required',
            'assignmentStart' => 'required|date|date-format:Y/m/d|before:assignmentEnd',
            'assignmentEnd' => 'required|date|date-format:Y/m/d|after:assignmentStart',
            'assignmentStartTime' => 'required',
            'assignmentEndTime' => 'required',
            'courses_id' => 'required'
        ]);

        $courses_id = $request->input('courses_id');

        //取得該課程所有授課教師
        foreach ($courses_id as $course_id){

            $teachers = DB::table('teacher_course')
                ->where('courses_id', $course_id)
                ->get();
            $teachers_id = $teachers->pluck('teachers_id');

            $start_time = date("H:i", strtotime($request->input('assignmentStartTime')));
            $end_time = date("H:i", strtotime($request->input('assignmentEndTime')));

            //新增作業
            $assignment = new Assignment([
                'name' => $request->input('assignmentName'),
                'start_date' => $request->input('assignmentStart'),
                'start_time' => $start_time,
                'end_date' => $request->input('assignmentEnd'),
                'end_time' => $end_time,
                'courses_id' => $course_id,
            ]);

            $assignment->save();

            //取得剛剛新增的作業id
            $assignment_id = $assignment->id;

            for($i=0; $i<count($teachers); $i++){
                DB::table('teacher_assignment')
                    ->insert(
                        ['teachers_id' => $teachers_id[$i], 'assignments_id' => $assignment_id]
                    );
            }

            //create assignment for students
            //create student_course first

            //get the course's student count
            $students = DB::table('student_course')->where('courses_id', $course_id)->get();
            $students_id = $students->pluck('students_id');

            for ($i=0; $i<count($students); $i++){

                DB::table('student_assignment')
                    ->insert([
                        ['students_id' => $students_id[$i], 'assignments_id' => $assignment_id]
                    ]);

                Storage::makeDirectory('public/'.$students_id[$i].'/'.$assignment_id);
            }
        }


        return redirect()->back()->with('message', '新增作業成功！');
    }


    public function getAllAssignments(){

        //找出這個老師的課程
        $courses = DB::table('teacher_course')
            ->get();

        $courses_id = $courses->pluck('courses_id');

        //找出這個老師的課程的作業
        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 1)
            ->get();

        $assignments_processing_id = $assignments_processing->pluck('id');

        //該作業的課程ID
        $courses_processing_id = $assignments_processing->pluck('courses_id');

        //該作業的名稱
        $assignments_processing_name = $assignments_processing->pluck('name');

        //該作業的更新時間
        $assignments_processing_update_at = $assignments_processing->pluck('updated_at');

        for($i=0; $i<count($assignments_processing_update_at); $i++){
            $assignments_processing_update_at[$i] = Carbon::parse($assignments_processing_update_at[$i])->diffForHumans();
        }

        //取得該作業的課程資訊
        $courses_processing_year = array();
        $courses_processing_semester = array();
        $courses_processing_start_date = array();
        $courses_processing_end_date = array();
        $course_processing_name= array();
        $common_course_processing_name = array();

        //取得該作業的指導老師
        $teachers_processing = array();

        for ($i=0; $i<count($assignments_processing); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_processing_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');
            $start_date = $common_courses_detail->value('start_date');
            $end_date = $common_courses_detail->value('end_date');

            array_push($courses_processing_year, $year);
            array_push($courses_processing_semester, $semester);
            array_push($courses_processing_start_date, $start_date);
            array_push($courses_processing_end_date, $end_date);

            array_push($common_course_processing_name, $common_course_name);
            array_push($course_processing_name, $course_name);


            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_processing, $teacher_array);

        }


        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 0)
            ->get();

        $assignments_finished_id = $assignments_finished->pluck('id');

        //該作業的課程ID
        $courses_finished_id = $assignments_finished->pluck('courses_id');

        //該作業的名稱
        $assignments_finished_name = $assignments_finished->pluck('name');

        //該作業的更新時間
        $assignments_finished_update_at = $assignments_finished->pluck('updated_at');

        for($i=0; $i<count($assignments_finished_update_at); $i++){
            $assignments_finished_update_at[$i] = Carbon::parse($assignments_finished_update_at[$i])->diffForHumans();
        }


        //取得該作業的課程資訊
        $courses_finished_year = array();
        $courses_finished_semester = array();
        $courses_finished_start_date = array();
        $courses_finished_end_date = array();
        $course_finished_name= array();
        $common_course_finished_name = array();

        //取得該作業的指導老師
        $teachers_finished = array();

        for ($i=0; $i<count($assignments_finished); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_finished_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');
            $start_date = $common_courses_detail->value('start_date');
            $end_date = $common_courses_detail->value('end_date');

            array_push($courses_finished_year, $year);
            array_push($courses_finished_semester, $semester);
            array_push($courses_finished_start_date, $start_date);
            array_push($courses_finished_end_date, $end_date);

            array_push($common_course_finished_name, $common_course_name);
            array_push($course_finished_name, $course_name);

            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_finished, $teacher_array);

        }

        return view('assignment.showAllAssignments', [
            'assignments_processing' => $assignments_processing,
            'assignments_processing_id' => $assignments_processing_id,
            'assignments_processing_name' => $assignments_processing_name,
            'assignments_processing_update_at' => $assignments_processing_update_at,
            'courses_processing_id' => $courses_processing_id,
            'courses_processing_year' => $courses_processing_year,
            'courses_processing_semester' => $courses_processing_semester,
            'courses_processing_start_date' => $courses_processing_start_date,
            'courses_processing_end_date' => $courses_processing_end_date,
            'courses_processing_name' => $course_processing_name,
            'common_course_processing_name' => $common_course_processing_name,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_id' => $assignments_finished_id,
            'assignments_finished_name' => $assignments_finished_name,
            'assignments_finished_updated_at' => $assignments_finished_update_at,
            'courses_finished_id' => $courses_finished_id,
            'courses_finished_year' => $courses_finished_year,
            'courses_finished_semester' => $courses_finished_semester,
            'courses_finished_start_date' => $courses_finished_start_date,
            'courses_finished_end_date' => $courses_finished_end_date,
            'courses_finished_name' => $course_finished_name,
            'common_course_finished_name' => $common_course_finished_name,
            'teachers_finished' => $teachers_finished,


        ]);    }



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

        //進行中的作業的狀態
        $assignments_processing_status = array();

        $assignments_processing_id = $assignments_processing->pluck('id');

        for ($r=0; $r<count($assignments_processing_id); $r++){
            $status = DB::table('student_assignment')
                ->where('assignments_id', $assignments_processing_id[$r])
                ->where('students_id', $student_id)
                ->value('status');
            array_push($assignments_processing_status, $status);
        }

        //進行中的作業的成績
        $assignments_processing_score = array();

        for ($r=0; $r<count($assignments_processing_id); $r++){
            $score = DB::table('student_assignment')
                ->where('assignments_id', $assignments_processing_id[$r])
                ->where('students_id', $student_id)
                ->value('score');
            array_push($assignments_processing_score, $score);
        }


        //取得assignment id 然後 hash
        $assignments_processing_id = $assignments_processing->pluck('id');
        for ($k=0; $k<count($assignments_processing_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_processing_id[$k] = $hashids->encode($assignments_processing_id[$k]);
        }

        //取得 assignment course id 然後 hash
        $assignments_processing_course_id = $assignments_processing->pluck('courses_id');
        for ($k=0; $k<count($assignments_processing_course_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $assignments_processing_course_id[$k] = $hashids->encode($assignments_processing_course_id[$k]);
        }

        // 基本資料
        $assignments_processing_name = $assignments_processing->pluck('name');
        $courses_processing_id = $assignments_processing->pluck('courses_id');
        $assignments_processing_end_date = $assignments_processing->pluck('end_date');
        $common_courses_processing = array();
        $course_processing_name= array();
        $common_course_processing_name = array();

        for ($i=0; $i<count($courses_processing_id); $i++){
            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_processing_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');

            $common_course_id = DB::table('courses')
                ->where('id', $courses_processing_id[$i])
                ->value('common_courses_id');
            $common_course_detail = DB::table('common_courses')
                ->where('id', $common_course_id)
                ->get();

            array_push($common_courses_processing, $common_course_detail);
            array_push($course_processing_name, $course_name);
            array_push($common_course_processing_name, $common_course_name);
        }



        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('id', $assignments_id)
            ->where('status', 0)
            ->get();

        //已結束的作業的狀態
        $assignments_finished_status = array();

        $assignments_finished_id = $assignments_finished->pluck('id');


        for ($r=0; $r<count($assignments_finished_id); $r++){
            $status = DB::table('student_assignment')
                ->where('assignments_id', $assignments_finished_id[$r])
                ->where('students_id', $student_id)
                ->value('status');
            array_push($assignments_finished_status, $status);
        }

        //已結束的作業的成績
        $assignments_finished_score = array();

        for ($r=0; $r<count($assignments_finished_id); $r++){
            $score = DB::table('student_assignment')
                ->where('assignments_id', $assignments_finished_id[$r])
                ->where('students_id', $student_id)
                ->value('score');
            array_push($assignments_finished_score, $score);
        }

        //取得 assignment id 然後 hash
        $assignments_finished_id = $assignments_finished->pluck('id');
        for ($k=0; $k<count($assignments_finished_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_finished_id[$k] = $hashids->encode($assignments_finished_id[$k]);
        }

        //取得 assignment course id 然後 hash
        $assignments_finished_course_id = $assignments_finished->pluck('courses_id');
        for ($k=0; $k<count($assignments_finished_course_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $assignments_finished_course_id[$k] = $hashids->encode($assignments_finished_course_id[$k]);
        }

        // 基本資料
        $assignments_finished_name = $assignments_finished->pluck('name');
        $courses_finished_id = $assignments_finished->pluck('courses_id');
        $assignments_finished_end_date = $assignments_finished->pluck('end_date');
        $common_courses_finished = array();
        $course_finished_name= array();
        $common_course_finished_name = array();

        for ($i=0; $i<count($courses_finished_id); $i++){
            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_finished_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');

            $common_course_id = DB::table('courses')
                ->where('id', $courses_finished_id[$i])
                ->value('common_courses_id');
            $common_course_detail = DB::table('common_courses')
                ->where('id', $common_course_id)
                ->get();

            array_push($common_courses_finished, $common_course_detail);
            array_push($common_course_finished_name, $common_course_name);
            array_push($course_finished_name, $course_name);
        }



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
            'assignments_processing_id' => $assignments_processing_id,
            'assignments_processing_course_id' => $assignments_processing_course_id,
            'assignments_processing_name' => $assignments_processing_name,
            'assignments_processing_status' => $assignments_processing_status,
            'assignments_processing_score' => $assignments_processing_score,
            'common_course_processing' => $common_courses_processing,
            'courses_processing'=>$courses_processing,
            'assignments_processing_end_date' => $assignments_processing_end_date,
            'teachers_processing' => $teachers_processing,
            'courses_processing_name' => $course_processing_name,
            'common_course_processing_name' => $common_course_processing_name,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_id' => $assignments_finished_id,
            'assignments_finished_course_id' => $assignments_finished_course_id,
            'assignments_finished_name' => $assignments_finished_name,
            'assignments_finished_status' => $assignments_finished_status,
            'assignments_finished_score' => $assignments_finished_score,
            'common_courses_finished' => $common_courses_finished,
            'courses_finished' => $courses_finished,
            'assignments_finished_end_date' => $assignments_finished_end_date,
            'teachers_finished' => $teachers_finished,
            'courses_finished_name' => $course_finished_name,
            'common_courses_finished_name' => $common_course_finished_name,

         ]);
    }



    public function getAssignments_Teacher(){
        $teacher_id = Auth::user()->id;

        //找出這個老師的課程
        $courses = DB::table('teacher_course')
            ->where('teachers_id', $teacher_id)
            ->get();

        $courses_id = $courses->pluck('courses_id');

        //找出這個老師的課程的作業
        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 1)
            ->get();

        $assignments_processing_id = $assignments_processing->pluck('id');

        //該作業的課程ID
        $courses_processing_id = $assignments_processing->pluck('courses_id');

        //該作業的名稱
        $assignments_processing_name = $assignments_processing->pluck('name');
        $assignments_processing_end_date = $assignments_processing->pluck('end_date');
        $assignments_processing_end_time = $assignments_processing->pluck('end_time');

        //取得該作業的課程資訊
        $courses_processing_year = array();
        $courses_processing_semester = array();
        $course_processing_name= array();
        $common_course_processing_name = array();

        //取得該作業的指導老師
        $teachers_processing = array();

        for ($i=0; $i<count($assignments_processing); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_processing_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');

            array_push($courses_processing_year, $year);
            array_push($courses_processing_semester, $semester);

            array_push($common_course_processing_name, $common_course_name);
            array_push($course_processing_name, $course_name);


            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_processing, $teacher_array);

        }


        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 0)
            ->get();

        $assignments_finished_id = $assignments_finished->pluck('id');

        //該作業的課程ID
        $courses_finished_id = $assignments_finished->pluck('courses_id');

        //該作業的名稱
        $assignments_finished_name = $assignments_finished->pluck('name');
        $assignments_finished_end_date = $assignments_finished->pluck('end_date');
        $assignments_finished_end_time = $assignments_finished->pluck('end_time');

        //取得該作業的課程資訊
        $courses_finished_year = array();
        $courses_finished_semester = array();
        $course_finished_name= array();
        $common_course_finished_name = array();

        //取得該作業的指導老師
        $teachers_finished = array();

        for ($i=0; $i<count($assignments_finished); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_finished_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');

            array_push($courses_finished_year, $year);
            array_push($courses_finished_semester, $semester);

            array_push($common_course_finished_name, $common_course_name);
            array_push($course_finished_name, $course_name);

            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_finished, $teacher_array);

        }

        //hash
        for ($k=0; $k<count($courses_processing_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $courses_processing_id[$k] = $hashids->encode($courses_processing_id[$k]);
        }

        for ($k=0; $k<count($assignments_processing_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_processing_id[$k] = $hashids->encode($assignments_processing_id[$k]);
        }

        for ($k=0; $k<count($courses_finished_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $courses_finished_id[$k] = $hashids->encode($courses_finished_id[$k]);
        }

        for ($k=0; $k<count($assignments_finished_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_finished_id[$k] = $hashids->encode($assignments_finished_id[$k]);
        }

        return view('assignment.showAssignments_Teacher', [
            'assignments_processing' => $assignments_processing,
            'assignments_processing_id' => $assignments_processing_id,
            'assignments_processing_name' => $assignments_processing_name,
            'assignments_processing_end_date' => $assignments_processing_end_date,
            'assignments_processing_end_time' => $assignments_processing_end_time,
           'courses_processing_id' => $courses_processing_id,
           'courses_processing_year' => $courses_processing_year,
           'courses_processing_semester' => $courses_processing_semester,
            'courses_processing_name' => $course_processing_name,
            'common_course_processing_name' => $common_course_processing_name,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_id' => $assignments_finished_id,
            'assignments_finished_name' => $assignments_finished_name,
            'assignments_finished_end_date' => $assignments_finished_end_date,
            'assignments_finished_end_time' => $assignments_finished_end_time,
            'courses_finished_id' => $courses_finished_id,
            'courses_finished_year' => $courses_finished_year,
            'courses_finished_semester' => $courses_finished_semester,
            'courses_finished_name' => $course_finished_name,
            'common_course_finished_name' => $common_course_finished_name,
            'teachers_finished' => $teachers_finished,


        ]);
    }



    public function getSingleAssignments_Teacher($common_courses_id, $courses_id){

        $encode_common_course_id = new Hashids('common_courses_id', 10);
        $encode_course_id = new Hashids('courses_id', 7);
        $common_courses_id = $encode_common_course_id->decode($common_courses_id); //用不到
        $courses_id = $encode_course_id->decode($courses_id);

        //找出這個老師的課程的作業
        //進行中的作業
        $assignments_processing = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 1)
            ->get();

        $assignments_processing_id = $assignments_processing->pluck('id');

        //該作業的課程ID
        $courses_processing_id = $assignments_processing->pluck('courses_id');

        //該作業的名稱
        $assignments_processing_name = $assignments_processing->pluck('name');
        $assignments_processing_end_date = $assignments_processing->pluck('end_date');
        $assignments_processing_end_time = $assignments_processing->pluck('end_time');

        //取得該作業的課程資訊
        $courses_processing_year = array();
        $courses_processing_semester = array();
        $course_processing_name= array();
        $common_course_processing_name = array();

        //取得該作業的指導老師
        $teachers_processing = array();

        for ($i=0; $i<count($assignments_processing); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_processing_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');

            array_push($courses_processing_year, $year);
            array_push($courses_processing_semester, $semester);

            array_push($common_course_processing_name, $common_course_name);
            array_push($course_processing_name, $course_name);


            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_processing_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_processing, $teacher_array);

        }


        //已結束的作業
        $assignments_finished = DB::table('assignments')
            ->whereIn('courses_id', $courses_id)
            ->where('status', 0)
            ->get();

        $assignments_finished_id = $assignments_finished->pluck('id');

        //該作業的課程ID
        $courses_finished_id = $assignments_finished->pluck('courses_id');

        //該作業的名稱
        $assignments_finished_name = $assignments_finished->pluck('name');
        $assignments_finished_end_date = $assignments_finished->pluck('end_date');
        $assignments_finished_end_time = $assignments_finished->pluck('end_time');

        //取得該作業的課程資訊
        $courses_finished_year = array();
        $courses_finished_semester = array();
        $course_finished_name= array();
        $common_course_finished_name = array();

        //取得該作業的指導老師
        $teachers_finished = array();

        for ($i=0; $i<count($assignments_finished); $i++) {

            //課程資訊
            $course = DB::table('courses')
                ->where('id', $courses_finished_id[$i]);

            $common_course_id = $course->value('common_courses_id');

            $common_courses_detail = DB::table('common_courses')
                ->where('id', $common_course_id);

            $common_course_name = $common_courses_detail->value('name');
            $course_name = $course->value('name');


            $year = $common_courses_detail->value('year');
            $semester = $common_courses_detail->value('semester');

            array_push($courses_finished_year, $year);
            array_push($courses_finished_semester, $semester);

            array_push($common_course_finished_name, $common_course_name);
            array_push($course_finished_name, $course_name);

            //指導老師
            $teachers = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->get();

            //指導老師的人數
            $teacher_count = DB::table('teacher_course')
                ->where('courses_id', $courses_finished_id[$i])
                ->count();

            $teacher_array = array();
            for ($j = 0; $j < $teacher_count; $j++) {
                $teacher_name = DB::table('teachers')
                    ->where('users_id', $teachers[$j]->teachers_id)
                    ->value('users_name');

                array_push($teacher_array, $teacher_name);
            }

            array_push($teachers_finished, $teacher_array);

        }

        //hash
        for ($k=0; $k<count($courses_processing_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $courses_processing_id[$k] = $hashids->encode($courses_processing_id[$k]);
        }

        for ($k=0; $k<count($assignments_processing_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_processing_id[$k] = $hashids->encode($assignments_processing_id[$k]);
        }

        for ($k=0; $k<count($courses_finished_id); $k++){
            $hashids = new Hashids('course_id', 6);
            $courses_finished_id[$k] = $hashids->encode($courses_finished_id[$k]);
        }

        for ($k=0; $k<count($assignments_finished_id); $k++){
            $hashids = new Hashids('assignment_id', 10);
            $assignments_finished_id[$k] = $hashids->encode($assignments_finished_id[$k]);
        }

        return view('assignment.showAssignments_Teacher', [
            'assignments_processing' => $assignments_processing,
            'assignments_processing_id' => $assignments_processing_id,
            'assignments_processing_name' => $assignments_processing_name,
            'assignments_processing_end_date' => $assignments_processing_end_date,
            'assignments_processing_end_time' => $assignments_processing_end_time,
            'courses_processing_id' => $courses_processing_id,
            'courses_processing_year' => $courses_processing_year,
            'courses_processing_semester' => $courses_processing_semester,
            'courses_processing_name' => $course_processing_name,
            'common_course_processing_name' => $common_course_processing_name,
            'teachers_processing' => $teachers_processing,

            'assignments_finished' => $assignments_finished,
            'assignments_finished_id' => $assignments_finished_id,
            'assignments_finished_name' => $assignments_finished_name,
            'assignments_finished_end_date' => $assignments_finished_end_date,
            'assignments_finished_end_time' => $assignments_finished_end_time,
            'courses_finished_id' => $courses_finished_id,
            'courses_finished_year' => $courses_finished_year,
            'courses_finished_semester' => $courses_finished_semester,
            'courses_finished_name' => $course_finished_name,
            'common_course_finished_name' => $common_course_finished_name,
            'teachers_finished' => $teachers_finished,


        ]);
    }


    public function getStudentAssignmentsList($course_id, $assignment_id){
        $encode_course_id = new Hashids('course_id', 6);
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $course_id = $encode_course_id->decode($course_id);
        $assignment_id = $encode_assignment_id->decode($assignment_id);

        //get an array from hashid::decode(), so we need to turn it to string
        $assignment_id = $assignment_id[0];

        $student_assignments = DB::table('student_assignment')
            ->where('assignments_id', $assignment_id)
            ->get();

        $student_assignments_id = $student_assignments->pluck('id');

        $student_ids = $student_assignments->pluck('students_id');
        $scores = $student_assignments->pluck('score');
        $remark = $student_assignments->pluck('remark');

        $updated_at = $student_assignments->pluck('updated_at');

        for ($i=0; $i< count($updated_at); $i++){
            if ($updated_at[$i] != null){
                $updated_at[$i] = Carbon::parse($updated_at[$i])->diffForHumans();
            } else {
                $updated_at[$i] = '尚未上傳';
            }
        }

        $student_names = array();

        for ($i=0; $i< count($student_ids); $i++){
            $name = DB::table('students')
                ->where('users_id', $student_ids[$i])
                ->value('users_name');
            array_push($student_names, $name);
        }

        $file_names = array();
        $file_urls = array();

        for ($i=0; $i< count($student_ids); $i++){

            $names = array();
            $urls = array();

            $folder_path = storage_path().'/app/public/'.$student_ids[$i].'/'.$assignment_id;

            $filesInFolder = File::files($folder_path);
            foreach($filesInFolder as $path) {
                $file = pathinfo($path);

                array_push($names, $file['filename'].'.'.$file['extension']) ;
                array_push($urls, ['public', $student_ids[$i], $assignment_id, $file['filename'].'.'.$file['extension']]);
            }

            array_push($file_names, $names);
            array_push($file_urls, $urls);
        }



        return view('assignment.showStudentAssignmentsList', [
            'students_assignments' => $student_assignments,
            'students_assignments_id' => $student_assignments_id,
            'student_ids' => $student_ids,
            'scores' => $scores,
            'remark' => $remark,
            'updated_at' => $updated_at,
            'student_names' => $student_names,
            'file_names' => $file_names,
            'file_urls' => $file_urls,
            'assignment_id' => $assignment_id,
        ]);
    }

    public function getAllAssignments_dt(){
        return DataTables::of(Assignment::query())
            ->editColumn('updated_at', function(Assignment $assignment){
                return $assignment->updated_at->diffForHumans();
            })
            ->make(true);
    }


    public function getHandInAssignment($course_id, $assignment_id){
        $student_id = Auth::user()->id;

        $encode_course_id = new Hashids('course_id', 6);
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $course_id = $encode_course_id->decode($course_id);
        $assignment_id = $encode_assignment_id->decode($assignment_id);

        $student_assignment = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->where('assignments_id', $assignment_id)
            ->first();

        $student_assignment_id = $student_assignment->id;

        $student_assignment_status = $student_assignment->status;

        $remark = $student_assignment->remark;

        $comment = $student_assignment->comment;

        $score = $student_assignment->score;

        return view('assignment.handInAssignment', [
            'course_id' => $course_id,
            'assignment_id' => $assignment_id,
            'student_assignment_id' => $student_assignment_id,
            'remark' => $remark,
            'comment' => $comment,
            'score' => $score,
            'student_assignment_status' => $student_assignment_status,
            ]);
    }

    public function postHandInAssignment(Request $request, $course_id, $assignment_id){
        $remark = $request->input('remark');
        $student_assignment_id = $request->input('student_assignment_id');
        DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->update(['remark' => $remark]);

        return redirect()->back()->with('message', '繳交作業成功！');
    }

    public function uploadAssignment(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->input('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $file = $request->file('file'); //default file name from request is "file"
        $filename = $file->getClientOriginalName();
        $filepath = $student_id.'/'.$assignment_id;

        $file->storeAs($filepath, $filename);

        Storage::disk('public')->putFileAs(
            $filepath, $file, $filename
        );

        DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->update(['status' => 2, 'updated_at' => Carbon::now()]);

    }

    public function deleteAssignmentFile(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $filename = $request->get('filename');

        $filepath = 'public/'.$student_id.'/'.$assignment_id.'/'.$filename;

        Storage::delete($filepath);

        $files = Storage::files('public/'.$student_id.'/'.$assignment_id);

        //如果資料夾內沒有檔案，將作業狀態更改為 1 => 未繳交
        if (empty($files)){
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['status' => 1]);
        }


        $output = array(
            'filepath' => $filepath,
            'student_assignment_id' => $student_assignment_id,
        );

        echo json_encode($output);
    }

    public function getAssignmentFileDetail(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->get('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $path = 'public/'.$student_id.'/'.$assignment_id;

        $filepaths = Storage::allFiles($path);

        $filenames = array();

        $filesizes = array();

        for($i=0; $i<count($filepaths); $i++){
            $filenames[$i] = basename($filepaths[$i]);
            $filesizes[$i] = Storage::size($filepaths[$i]);
        }

        $output = array(
            'filepaths' => $filepaths,
            'filenames' => $filenames,
            'filesizes' => $filesizes,
        );

        echo json_encode($output);


    }

    public function downloadAssignment($first, $second, $third, $fourth){
//        return Storage::download('public/505102236/1/midterm.py');

        $filepath = $first.'/'.$second.'/'.$third.'/'.$fourth;
//        return Storage::download($filepath);

        return response()->download(storage_path().'/app/'.$filepath);
    }

    public function correctAssignment(Request $request){
        $validation = Validator::make($request->all(), [
            'score' => 'Integer',
        ]);

        $error_array = array();
        $success_output = '';
        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {
            $student_assignment_id = $request->get('student_assignment_id');
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['score' => $request->get('score'), 'comment' =>$request->get('comment'), 'status' => 3]);
            $success_output = '<div class="alert alert-success"> 批改成功！ </div>';
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output
        );
        echo json_encode($output);
    }


}
