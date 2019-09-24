<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\common_course;
use App\Course;
use App\Student;
use App\ta_course;
use App\Teacher;
use App\teacher_course;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Hashids\Hashids;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class AssignmentController extends Controller
{

    //新增刪除
    public function getCreateAssignment(){
        $teacher = Teacher::where('users_id', Auth::user()->id)->first();
        //取得該課程ID
        $courses_id = $teacher->course()->pluck('id');
        $courses = $teacher->course()->get();
        foreach($courses as $course){
            $course->student()->get();
        }

        return view('assignment.createAssignment', [
            'test' => $courses_id
        ]);
    }

    public function postCreateAssignment(Request $request){
        $request->validate([
            'assignmentName' => [
                'required',
//TODO  作業名稱驗證              function($attribute, $value, $fail) {
//                    $teacher = Teacher::where('users_id', Auth::user()->id)->first();
//                    //取得其中一個進行中的課程
//                    $course = $teacher->course()
//                        ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
//                        ->select('common_courses.semester as semester', 'common_courses.year as year')
//                        ->where('status', 1)
//                        ->first();
//                    $detail = collect();
//                    //找出同名的作業
//                    $assignments = Assignment::where('name', $value)->get();
//                    foreach($assignments as $assignment){
//                        $assignment_detail = $assignment->course()
//                            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
//                            ->select('common_courses.semester as semester', 'common_courses.year as year')
//                            ->first();
//                        $detail->push($assignment_detail);
//                        if(($assignment_detail->year == $course->year) and ($assignment_detail->year == $course->semester)){
//                            return $fail('錯誤：當前學期已存在此作業');
//                        };
//                    }
//                    dd($detail);
//                },
            ],
            'assignmentStart' => 'required|date|date-format:Y/m/d|before:assignmentEnd',
            'assignmentEnd' => 'required|date|date-format:Y/m/d|after:assignmentStart',
            'assignmentStartTime' => 'required',
            'assignmentEndTime' => 'required',
//            'assignmentPercentage' => [
//                'required',
//                'between:0,99.99',
//                function($attribute, $value, $fail) {
//                    $teacher = Teacher::where('users_id', Auth::user()->id)->first();
//                    $percentages = $teacher->course()->first()->assignment->pluck('percentage');
//
//                    $total_percentage = 0;
//                    foreach($percentages as $percentage){
//                        $total_percentage += $percentage;
//                    }
//
//                    $total_percentage += $value;
//
//                    if ($total_percentage > 100) {
//                        $overPercentage = floor(($total_percentage*100)-10000)/100;
//                        return $fail('錯誤：總比率為 '.$total_percentage.'% ,超過 '.$overPercentage.' %');
//                    }
//                },
//            ]
        ]);

        $teacher = Teacher::where('users_id', Auth::user()->id)->first();

        //取得進行中的課程
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id as course_id', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

        $start_time = date("H:i", strtotime($request->input('assignmentStartTime')));
        $end_time = date("H:i", strtotime($request->input('assignmentEndTime')));

        $hide = $request->input('hide');
        if ($hide){
            $hide = true;
        } else {
            $hide = false;
        }

        $announceScore = $request->input('notAnnounceScore');
        if ($announceScore){
            $announceScore = false;
        } else {
            $announceScore = true;
        }

        //新增作業
        $assignments_id = array();
        foreach($courses as $course){
            $assignment_id = DB::table('assignments')
                ->insertGetId( [
                    'name' => $request->input('assignmentName'),
                    'content' => $request->input('assignmentContent'),
                    'start_date' => $request->input('assignmentStart'),
                    'start_time' => $start_time,
                    'end_date' => $request->input('assignmentEnd'),
                    'end_time' => $end_time,
                    'courses_id' => $course->course_id,
//                    'percentage' => $request->input('assignmentPercentage'),
                    'hide' => $hide,
                    'announce_score' => $announceScore,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            array_push($assignments_id, $assignment_id);
        }


        //create assignment for students
        //so need to create student_assignment
        foreach($courses as $course){
            //get this course's students
            $students_id = $course->student()->pluck('users_id');

            //get this course's assignments
            $assignments_id = $course->assignment()->pluck('id');

            //add new student_assignment
            foreach($assignments_id as $assignment_id){
                foreach($students_id as $student_id){
                    DB::table('student_assignment')
                        ->insert([
                            ['students_id' => $student_id, 'assignments_id' => $assignment_id]
                        ]);
                    Storage::makeDirectory('public/'.$student_id.'/'.$assignment_id);
                }
            }
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }

    public function deleteAssignment($id){
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $assignment_id = $encode_assignment_id->decode($id);

        $assignment = Assignment::where('id', $assignment_id)->first();
        $teacher = $assignment->course()->first()->teacher()->first();
        $courses = $teacher->course()->get();

        foreach($courses as $course){
            //get this course's students
            $students_id = $course->student()->pluck('users_id');

            //get this course's assignments
            //!! Should get the assignments which name is same as assignment->name
            $assignments_id = $course->assignment()->where('name', $assignment->name)->pluck('id');

            foreach($assignments_id as $assignment_id){
                DB::table('assignments')
                    ->where('id', $assignment_id)
                    ->delete();

                //delete student's assignment folder
                foreach($students_id as $student_id){
                    File::deleteDirectory(public_path($student_id.'/'.$assignment_id));
                }
            }
        }

        return redirect()->back()->with('message', '刪除作業成功！');
    }

    public function getBatchCreateAssignments(){
        //確認該 one 是否存在 many
        $common_courses = common_course::with('course')->get();

        //把不含 course 的 common course 刪掉（過濾）
        // (2019/3/2 fixed) 不需要!!因為 with('course') 已經過濾過了

//        for ($i=0; $i< count($common_courses); $i++){
//            if (! $common_courses[$i]->course()->exists()){
//                $common_courses->forget($i);
//            }
//        }


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
            'assignmentName' => 'required|unique:assignments,name',
            'assignmentName' => 'required',
            'assignmentPercentage' => 'required|numeric',
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

            $hide = $request->input('hide');

            if ($hide){
                $hide = true;
            } else {
                $hide = false;
            }

            $announceScore = $request->input('notAnnounceScore');
            if ($announceScore){
                $announceScore = false;
            } else {
                $announceScore = true;
            }

            //新增作業

            $assignment_id = DB::table('assignments')
                ->insertGetId( [
                    'name' => $request->input('assignmentName'),
                    'content' => $request->input('assignmentContent'),
                    'start_date' => $request->input('assignmentStart'),
                    'start_time' => $start_time,
                    'end_date' => $request->input('assignmentEnd'),
                    'end_time' => $end_time,
                    'courses_id' => $course_id,
                    'percentage' => $request->input('assignmentPercentage'),
                    'hide' => $hide,
                    'announce_score' => $announceScore,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            for($i=0; $i<count($teachers); $i++){
                DB::table('teacher_assignment')
                    ->insert([
                        'teachers_id' => $teachers_id[$i],
                        'assignments_id' => $assignment_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
            }

            //create assignment for students
            //create student_course first

            //get the course's student count
            $students = DB::table('student_course')->where('courses_id', $course_id)->get();
            $students_id = $students->pluck('students_id');

            for ($i=0; $i<count($students); $i++){

                DB::table('student_assignment')
                    ->insert([
                        'students_id' => $students_id[$i],
                        'assignments_id' => $assignment_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

                Storage::makeDirectory('public/'.$students_id[$i].'/'.$assignment_id);
            }
        }

        return redirect()->back()->with('message', '新增作業成功！');
    }


    //管理
    public function getManageAssignments_Teacher(){
        $user_id = Auth::user()->id;
        $teacher = Teacher::where('users_id', $user_id)->first();

        //取得進行中的課程
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

        foreach ($courses as $course){
            $assignments = $course->assignment()
                ->join('courses', 'courses.id', 'assignments.courses_id')
                ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
                ->select('courses.name as course_name',
                    'common_courses.name as common_course_name',
                    'common_courses.year',
                    'common_courses.semester',
                    'common_courses.status as common_course_status',
                    'assignments.id as assignment_id',
                    'assignments.name as assignment_name',
                    'assignments.status as assignment_status',
                    'assignments.percentage as assignment_percentage',
                    'assignments.start_date',
                    'assignments.end_date',
                    'assignments.updated_at')
                ->get();

            //hashids
            foreach ($assignments as $assignment){
                $hashids = new Hashids('assignment_id', 10);
                $assignment->assignment_id = $hashids->encode($assignment->assignment_id);
            }
            $course->assignments = $assignments;
        }

        return view('assignment.manageAssignments_Teacher', [
            'courses' => $courses,
        ]);
    }

    //列出
    public function getAllAssignments(){

        $assignments = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
            ->select('courses.name as course_name',
                'common_courses.name as common_course_name',
                'common_courses.year',
                'common_courses.semester',
                'common_courses.status as common_course_status',
                'assignments.name as assignment_name',
                'assignments.status as assignment_status',
                'assignments.percentage as assignment_percentage',
                'assignments.start_date',
                'assignments.end_date',
                'assignments.updated_at')
            ->get();

        //for grade adjustment
        $assignments_adjust = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
            ->select('assignments.*', 'common_courses.year as year', 'common_courses.semester as semester')
            ->where('common_courses.status', 1)
            ->get();

        //for getting the detail of course
        $assignments_first = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
            ->select('assignments.*', 'common_courses.year as year', 'common_courses.semester as semester')
            ->where('common_courses.status', 1)
            ->first();

        //if assignment is empty, redirect back
        if ($assignments_first == null){
            return redirect()->back()->with(['message' => '當學期沒有進行中的作業']);
        }

        $year = $assignments_first->year;
        $semester = $assignments_first->semester;



        $assignments_a4 = collect();
        $assignments_attendance = collect();
        $assignments_classParticipation = collect();
        $assignments_ppt = collect();
        $assignments_word = collect();
        $assignments_a4_id = array();
        $assignments_attendance_id = array();
        $assignments_classParticipation_id = array();
        $assignments_ppt_id = array();
        $assignments_word_id = array();

        //107 年 第1學期只有四個作業
        if ($year == 107 and $semester == 1){
            foreach($assignments_adjust as $key => $assignment){

                //get the id of each homework
                //not include teacher's custom homework ex. 課堂作業
                if ($assignment->name == 'A4海報'){
                    $assignments_a4->push($assignment);
                    array_push($assignments_a4_id, $assignment->id);
                } else if ($assignment->name == '上課出席'){
                    $assignments_attendance->push($assignment);
                    array_push($assignments_attendance_id, $assignment->id);

                } else if ($assignment->name == '口頭報告與PPT'){
                    $assignments_ppt->push($assignment);
                    array_push($assignments_ppt_id, $assignment->id);

                } else if ($assignment->name == '書面報告Word'){
                    $assignments_word->push($assignment);
                    array_push($assignments_word_id, $assignment->id);
                }
            }
        } else {
            foreach($assignments_adjust as $key => $assignment){

                //get the id of each homework
                //not include teacher's custom homework ex. 課堂作業
                if ($assignment->name == 'A4海報'){
                    $assignments_a4->push($assignment);
                    array_push($assignments_a4_id, $assignment->id);
                } else if ($assignment->name == '上課出席'){
                    $assignments_attendance->push($assignment);
                    array_push($assignments_attendance_id, $assignment->id);
                } else if ($assignment->name == '課堂參與'){
                    $assignments_classParticipation->push($assignment);
                    array_push($assignments_classParticipation_id, $assignment->id);
                } else if ($assignment->name == '口頭報告與PPT'){
                    $assignments_ppt->push($assignment);
                    array_push($assignments_ppt_id, $assignment->id);

                } else if ($assignment->name == '書面報告Word'){
                    $assignments_word->push($assignment);
                    array_push($assignments_word_id, $assignment->id);
                }
            }
        }


        return view('assignment.showAllAssignments', [
            'assignments' => $assignments,
            'assignments_a4' => $assignments_a4,
            'assignments_attendance' => $assignments_attendance,
            'assignments_classParticipation' => $assignments_classParticipation,
            'assignments_ppt' => $assignments_ppt,
            'assignments_word' => $assignments_word,
            'assignments_a4_id' => $assignments_a4_id,
            'assignments_attendance_id' => $assignments_attendance_id,
            'assignments_classParticipation_id' => $assignments_classParticipation_id,
            'assignments_ppt_id' => $assignments_ppt_id,
            'assignments_word_id' => $assignments_word_id,

            'year' => $year,
            'semester' => $semester,
        ]);
    }

    public function getAssignments(){
        $user = Auth::user();

        $student = Student::where('users_id', $user->id)->first();
        $courses = $student->course()
            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
            ->select('common_courses.id as common_course_id', 'common_courses.name as common_course_name',
                'common_courses.year as year', 'common_courses.semester as semester',
                'courses.*', 'courses.id as course_id', 'courses.name as course_name')
            ->where('common_courses.status', 1)
            ->get();

        foreach ($courses as $course){
            $assignments = $course->assignment()
                ->select('assignments.*', 'assignments.id as assignment_id', 'assignments.name',
                    'assignments.end_date', 'assignments.end_time')
                ->get();

            $hashids_course = new Hashids('course_id', 6);
            $course->course_id = $hashids_course->encode($course->course_id);

            $course->assignment = $assignments;

            foreach($course->assignment as $assignment){
                $hashids_assignment = new Hashids('assignment_id', 10);
                $assignment->assignment_id = $hashids_assignment->encode($assignment->assignment_id);
                $assignment->student = $assignment->student()
                    ->withPivot(['score', 'status', 'makeUpDate'])
                    ->select('student_assignment.id as student_assignment_id','student_assignment.score', 'student_assignment.status', 'student_assignment.makeUpDate')
                    ->where('users_id', $user->id)
                    ->first();
            }
        }

        return view('assignment.showAssignments', [
            'courses' => $courses
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

        $course = Course::where('id', $courses_id)->first();
        $assignments_processing = $course->assignment()
            ->join('courses', 'courses.id', 'assignments.courses_id')
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('common_courses.year as year', 'common_courses.semester as semester', 'common_courses.name as common_course_name',
                'courses.name as course_name', 'courses.id as course_id',
                'assignments.id as assignment_id', 'assignments.name as assignment_name',
                'assignments.end_date as assignment_end_date', 'assignments.end_time as assignment_end_time')
            ->where('assignments.status', 1)
            ->get();

        $assignments_finished = $course->assignment()
            ->join('courses', 'courses.id', 'assignments.courses_id')
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('common_courses.year as year', 'common_courses.semester as semester',
                'common_courses.name as common_course_name', 'courses.name as course_name',
                'assignments.name as assignment_name', 'assignments.end_date as assignment_end_date',
                'assignments.end_time as assignment_end_time')
            ->where('assignments.status', 0)
            ->get();

        foreach($assignments_processing as $assignment){
            $hashids_assignment = new Hashids('assignment_id', 10);
            $hashids_course = new Hashids('course_id', 6);

            $assignment->assignment_id = $hashids_assignment->encode($assignment->assignment_id);
            $assignment->course_id = $hashids_course->encode($assignment->course_id);
        }

        foreach($assignments_finished as $assignment){
            $hashids_assignment = new Hashids('assignment_id', 10);
            $hashids_course = new Hashids('course_id', 6);

            $assignment->assignment_id = $hashids_assignment->encode($assignment->assignment_id);
            $assignment->course_id = $hashids_course->encode($assignment->course_id);
        }

        return view('assignment.showAssignments_Teacher', [
            'assignments_processing' => $assignments_processing,
            'assignments_finished' => $assignments_finished,
        ]);
    }

    public function getCourseAssignments_Student($courses_id){

        $encode_course_id = new Hashids('courses_id', 7);
        $courses_id = $encode_course_id->decode($courses_id);

        $student = Student::where('users_id', Auth::user()->id)->first();

        $assignments_processing = $student->assignment()
            ->withPivot(['score', 'comment'])
            ->join('courses', 'courses.id', '=', 'assignments.courses_id')
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('common_courses.year as year', 'common_courses.semester as semester',
                'common_courses.name as common_course_name', 'courses.name as course_name',
                'courses.id as course_id', 'assignments.id as assignment_id', 'assignments.name as assignment_name',
                'assignments.status as assignment_status', 'student_assignment.score as score', 'student_assignment.status as student_assignment_status',
                'assignments.end_date as end_date', 'assignments.end_time as end_time', 'assignments.announce_score as announce_score',
                'assignments.hide as hide')
            ->where('assignments.status', 1)
            ->where('courses.id', $courses_id)
            ->get();

        $assignments_finished = $student->assignment()
            ->withPivot(['score', 'comment'])
            ->join('courses', 'courses.id', '=', 'assignments.courses_id')
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('common_courses.year as year', 'common_courses.semester as semester',
                'common_courses.name as common_course_name', 'courses.name as course_name',
                'courses.id as course_id', 'assignments.id as assignment_id', 'assignments.name as assignment_name',
                'assignments.status as assignment_status', 'student_assignment.score as score', 'student_assignment.status as student_assignment_status',
                'assignments.end_date as end_date', 'assignments.end_time as end_time', 'assignments.announce_score as announce_score',
                'assignments.hide as hide')
            ->where('assignments.status', 0)
            ->where('courses.id', $courses_id)
            ->get();

        foreach($assignments_processing as $assignment){
            $hashids_assignment = new Hashids('assignment_id', 10);
            $hashids_course = new Hashids('course_id', 6);

            $assignment->assignment_id = $hashids_assignment->encode($assignment->assignment_id);
            $assignment->course_id = $hashids_course->encode($assignment->course_id);
        }

        foreach($assignments_finished as $assignment){
            $hashids_assignment = new Hashids('assignment_id', 10);
            $hashids_course = new Hashids('course_id', 6);

            $assignment->assignment_id = $hashids_assignment->encode($assignment->assignment_id);
            $assignment->course_id = $hashids_course->encode($assignment->course_id);
        }


        return view('assignment.showCourseAssignments_Student', [
            'assignments_processing' => $assignments_processing,
            'assignments_finished' => $assignments_finished,
        ]);
    }

    public function getStudentAssignmentsList($course_id, $assignment_id){
        $encode_course_id = new Hashids('course_id', 6);
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $course_id = $encode_course_id->decode($course_id);
        $assignment_id = $encode_assignment_id->decode($assignment_id);

        //get an array from hashid::decode(), so we need to turn it to string
        $assignment_id = $assignment_id[0];

        $assignment_status = DB::table('assignments')
            ->where('id', $assignment_id)
            ->value('status');

        $student_assignments = DB::table('student_assignment')
            ->where('assignments_id', $assignment_id)
            ->get();

        $student_assignments_id = $student_assignments->pluck('id');

        $student_ids = $student_assignments->pluck('students_id');
        $scores = $student_assignments->pluck('score');
        $remark = $student_assignments->pluck('remark');
        $student_assignment_status = $student_assignments->pluck('status');

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

            if (!File::exists($folder_path) ){
                File::makeDirectory($folder_path, $mode = 0777, true, true);
            }

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
            'student_assignment_status' => $student_assignment_status,
            'updated_at' => $updated_at,
            'student_names' => $student_names,
            'file_names' => $file_names,
            'file_urls' => $file_urls,
            'assignment_id' => $assignment_id,
            'assignment_status' => $assignment_status
        ]);
    }

    public function getAllAssignments_dt(){
        return DataTables::of(Assignment::query())
            ->editColumn('updated_at', function(Assignment $assignment){
                return $assignment->updated_at->diffForHumans();
            })
            ->make(true);
    }

    //批改
    public function getCorrectAssignment(){
        $user = Auth::user();
        $teachers = Teacher::all();

        if ($user->type == 0){
            $teacherID = Input::get('teacherID');
            $teacher = Teacher::where('users_id', $teacherID)->first();
        } else if ($user->type == 2){ //TA
            $ta = $user->ta()->first();
            $ta->course_id = $ta->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
                ->where('status', 1)
                ->pluck('id');

            //讓助教可以選自己的老師來改
            $teachers = collect();
            foreach($ta->course_id as $course_id){
                $teachers->push(Course::where('id', $course_id)->first()->teacher()->first());
                $teachers = $teachers->unique('users_name');
            }

            $teacherID = Input::get('teacherID');
            $teacher = Teacher::where('users_id', $teacherID)->first();
        } else {
            $teacher = Teacher::where('users_id', $user->id)->first();
        }

        //取得進行中的課程
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

        //get all assignments of teacher
        $assignments = collect();

        foreach($courses as $course){
            if ($course->assignment()->get()->isNotEmpty()){
                $assignments->push($course->assignment()->get());
            }
        }

        //if assignment is empty, redirect back
        if ($assignments->isEmpty()){
            return redirect()->back()->with(['message' => '當學期沒有進行中的作業']);
        }

        //pluck assignment_id
        $assignments_id = array();
        foreach($assignments as $assignment){
            array_push($assignments_id, $assignment->pluck('id')->toArray());
        }

        //flatten the 2-dimensional array to 1-dimensional array
        $assignments_id = call_user_func_array('array_merge', $assignments_id);


        //get all student_assignments
        $student_assignments = collect();
        foreach($assignments_id as $assignment_id){
            $student_assignment = DB::table('student_assignment')
                ->where('assignments_id', $assignment_id)
                ->get();
            $student_assignments->push($student_assignment);
        }

        //pluck assignment id from student_assignment
        $student_assignment_assignments_id = array();
        $student_assignment_status = array();
        foreach($student_assignments as $student_assignment){
            array_push($student_assignment_assignments_id, $student_assignment->pluck('assignments_id')->toArray());
            array_push($student_assignment_status, $student_assignment->pluck('status')->toArray());

        }

        //flatten the 2-dimensional array to 1-dimensional array
        $student_assignment_assignments_id = call_user_func_array('array_merge', $student_assignment_assignments_id);
        $student_assignment_status = call_user_func_array('array_merge', $student_assignment_status);

        //get the common course name and status
        $common_courses_name = array();
        $common_courses_status = array();

        foreach($student_assignment_assignments_id as $assignment_id){
            $assignment = Assignment::where('id', $assignment_id)->first();
            $common_course = $assignment->course()->first()->common_course()->first();
            $common_course_name = $common_course->name;
            $common_course_status = $common_course->status;

            array_push($common_courses_name, $common_course_name);
            array_push($common_courses_status, $common_course_status);

        }

        //get assignment name
        $assignments_name = array();
        $assignments_status = array();
        foreach($student_assignment_assignments_id as $assignment_id){
            $assignment = Assignment::where('id', $assignment_id)->first();
            $assignment_name = $assignment->name;
            $assignment_status = $assignment->status;
            array_push($assignments_name, $assignment_name);
            array_push($assignments_status, $assignment_status);
        }


        //pluck student_assignment_id
        $student_assignments_id = array();
        foreach($student_assignments as $student_assignment){
            array_push($student_assignments_id, $student_assignment->pluck('id')->toArray());
        }

        //flatten the 2-dimensional array to 1-dimensional array
        $student_assignments_id = call_user_func_array('array_merge', $student_assignments_id);


        $student_ids = array();
        $scores = array();
        $titles = array();
        $updated_at = array();
        $comments = array();
        $makeupDate = array();

        foreach($student_assignments as $student_assignment){
            array_push($student_ids, $student_assignment->pluck('students_id')->toArray());
            array_push($scores, $student_assignment->pluck('score')->toArray());
            array_push($titles, $student_assignment->pluck('title')->toArray());
            array_push($updated_at, $student_assignment->pluck('updated_at')->toArray());
            array_push($comments, $student_assignment->pluck('comment')->toArray());
            array_push($makeupDate, $student_assignment->pluck('makeUpDate')->toArray());
        }

        //flatten the 2-dimensional array to 1-dimensional array
        $student_ids = call_user_func_array('array_merge', $student_ids);
        $scores = call_user_func_array('array_merge', $scores);
        $titles = call_user_func_array('array_merge', $titles);
        $updated_at = call_user_func_array('array_merge', $updated_at);
        $comments = call_user_func_array('array_merge', $comments);
        $makeupDate = call_user_func_array('array_merge', $makeupDate);


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

        for ($i=0; $i< count($student_assignments_id); $i++){

            $names = array();
            $urls = array();

            $folder_path = storage_path().'/app/public/'.$student_ids[$i].'/'.$student_assignment_assignments_id[$i];

            if (!File::exists($folder_path) ){
                File::makeDirectory($folder_path, $mode = 0777, true, true);
            }

            setlocale(LC_ALL,'en_US.UTF-8');

            Log::info($folder_path);
            $filesInFolder = File::files($folder_path);
            Log::info($filesInFolder);
            foreach($filesInFolder as $path) {
                $file = pathinfo($path);

                if($file['filename'] != 'blob'){ //空的檔案
                    array_push($names, $file['filename'].'.'.$file['extension']) ;
                    array_push($urls, ['public', $student_ids[$i], $student_assignment_assignments_id[$i], $file['filename'].'.'.$file['extension']]);
                }
            }

            array_push($file_names, $names);
            array_push($file_urls, $urls);
        }

        $notHandIn = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->where('status', 1)
            ->count();

        $finishedHandIn = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->where('status', 2)
            ->count();

        $corrected = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->where('status', 3)
            ->count();

        $notMakeUp = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->where('status', 4)
            ->count();

        $finishedMakeUp = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->where('status', 5)
            ->count();

        $finished = $finishedHandIn + $finishedMakeUp + $corrected;
        $notFinished = $notMakeUp + $notHandIn;
        $all = DB::table('student_assignment')
            ->where('assignments_id', $student_assignments_id)
            ->count();

        return view('assignment.correctAssignment', [
            'user' => $user,
            'student_assignments' => $student_assignments,
            'student_assignments_id' => $student_assignments_id,
            'student_ids' => $student_ids,
            'scores' => $scores,
            'titles' => $titles,
            'makeUpDate' => $makeupDate,
            'updated_at' => $updated_at,
            'student_names' => $student_names,
            'file_names' => $file_names,
            'file_urls' => $file_urls,
            'finishedHandIn' => $finishedHandIn,
            'finished' => $finished,
            'notFinished' => $notFinished,
            'all' => $all,
            'teachers' => $teachers,
            'teacher' => $teacher,
            'courses' => $courses,
            'comments' => $comments,
            'assignments' => $assignments,
            'assignments_id' => $assignments_id,
            'common_courses_name' => $common_courses_name,
            'common_courses_status' => $common_courses_status,
            'student_assignment_assignments_id' => $student_assignment_assignments_id,
            'student_assignment_status' => $student_assignment_status,
            'assignments_name' => $assignments_name,
            'assignments_status' => $assignments_status,

        ]);
    }

    //重繳
    public function postOpenMakeUpAssignment(Request $request){
        $validation = Validator::make($request->all(), [
            'student_assignment_id' => 'required',
            'makeUpDate' => 'required',
        ]);

        $error_array = array();
        $success_output = '';
        $student_assignment_id = $request->get('student_assignment_id');
        $makeUpDate = $request->get('makeUpDate');
        if ($validation->fails()){
            foreach($validation->messages()->getMessages() as $field_name => $messages)
            {
                $error_array[] = $messages;
            }
        } else {

            $date = $makeUpDate[0];
            $time = date("H:i", strtotime($makeUpDate[1]));
            $dateTime = $date.' '.$time;
            Log::info($dateTime);
            $timestamp = Carbon::createFromFormat('Y/m/d H:i', $dateTime);
            Log::info($timestamp);

            $student_assignment_id = $request->get('student_assignment_id');
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['status' => 4, 'makeUpDate' => $timestamp]);
            $success_output = '<div class="alert alert-success"> 成功開放補繳！ </div>';
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
            'id' => $student_assignment_id,
        );
        return $output;
    }


    public function getChangeAssignmentStatus($student_assignment_id, $status){
        DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->update(['status' => $status]);

        return redirect()->back()->with('message', '開放繳交成功！');
    }

    public function getHandInAssignment($course_id, $assignment_id){
        $student_id = Auth::user()->id;
        $notExpire = 1;

        $encode_course_id = new Hashids('course_id', 6);
        $encode_assignment_id = new Hashids('assignment_id', 10);
        $course_id = $encode_course_id->decode($course_id);
        $assignment_id = $encode_assignment_id->decode($assignment_id);
        $assignment= DB::table('assignments')
            ->where('id', $assignment_id)
            ->first();


        $student_assignment = DB::table('student_assignment')
            ->where('students_id', $student_id)
            ->where('assignments_id', $assignment_id)
            ->first();

        $student_assignment_id = $student_assignment->id;

        $student_assignment_status = $student_assignment->status;

        $student_assignment_makeUpDate = $student_assignment->makeUpDate;

        $title = $student_assignment->title;

        $comment = $student_assignment->comment;

        $score = $student_assignment->score;

        if ($student_assignment_status == 4 || $student_assignment_status == 5){ //確認補繳日期過了沒
            $notExpire = Carbon::now()->lt($student_assignment_makeUpDate) ? true : false;
            if ($notExpire){
                $notExpire = 1;
            } else {
                $notExpire = 0;
            }
        }

        //get assignment file detail
        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $path = 'public/'.$student_id.'/'.$assignment_id;

        $filepaths = Storage::allFiles($path);

        $filenames = array();

        $filesizes = array();

        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');

        for($i=0; $i<count($filepaths); $i++){
            $filenames[$i] = basename($filepaths[$i]);
            $filesizes[$i] = Storage::size($filepaths[$i]);
        }

        $files = array(
            'filepaths' => $filepaths,
            'filenames' => $filenames,
            'filesizes' => $filesizes,
        );

        return view('assignment.handInAssignment', [
            'course_id' => $course_id,
            'assignment_id' => $assignment_id,
            'student_assignment' => $student_assignment,
            'student_assignment_id' => $student_assignment_id,
            'title' => $title,
            'comment' => $comment,
            'score' => $score,
            'student_assignment_status' => $student_assignment_status,
            'student_assignment_makeUpDate' => $student_assignment_makeUpDate,
            'files' => $files,
            'assignment' => $assignment,
            'notExpire' => $notExpire
            ]);
    }

    public function postHandInAssignment(Request $request, $course_id, $assignment_id){
        $student_id = Auth::user()->id;
        $title = $request->input('title');
        $student_assignment_id = $request->input('student_assignment_id');
        $assignment_id = $request->input('assignment_id');
        $files = $request->file('file'); //default file name from request is "file"

        $status = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('status');

        if ($status == 1){ //未繳交
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'status' => 2, 'updated_at' => Carbon::now()]);

        } else if ($status == 2){ //已繳交
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'updated_at' => Carbon::now()]);

        } else if ($status == 3){ //教師已批改
            // 不會有這個情況, 上傳功能是被禁止的

        } else if ($status == 4){ //教師開放補繳
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'status' => 5, 'updated_at' => Carbon::now()]);

        } else if ($status == 5){ //已經補繳
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'updated_at' => Carbon::now()]);

        } else if ($status == 6){ //教師開放重繳
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'status' => 7, 'updated_at' => Carbon::now()]);
        } else if ($status == 7){
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['title' => $title, 'updated_at' => Carbon::now()]);
        }

        if ($files){
            foreach ($files as $file){
                $filename = $file->getClientOriginalName();
                $filepath = $student_id.'/'.$assignment_id;

                $filename = str_replace(' ', '_', $filename);
                //this line is really important!!!!!!!!!!!!!!
                setlocale(LC_ALL,'en_US.UTF-8');

                Storage::disk('public')->putFileAs(
                    $filepath, $file, $filename
                );
            }

        }



        return redirect()->back()->with('message', '繳交作業成功！');
    }


    //上傳
    public function uploadAssignment(Request $request){
        $student_id = Auth::user()->id;
        $student_assignment_id = $request->input('student_assignment_id');

        $assignment_id = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('assignments_id');

        $files = $request->file('file'); //default file name from request is "file"

        foreach($files as $file){
            $filename = $file->getClientOriginalName();
            $filepath = $student_id.'/'.$assignment_id;

            $filename = str_replace(' ', '_', $filename);
            //this line is really important!!!!!!!!!!!!!!
            setlocale(LC_ALL,'en_US.UTF-8');

            Storage::disk('public')->putFileAs(
                $filepath, $file, $filename
            );
        }

        //如果 title 已經存在 (學生填寫過內容了)，就把狀態改成已繳交
        $old_remark = DB::table('student_assignment')
            ->where('id', $student_assignment_id)
            ->value('title');
        if ($old_remark != null){
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['status' => 2,'updated_at' => Carbon::now()]);
        } else {
            DB::table('student_assignment')
                ->where('id', $student_assignment_id)
                ->update(['updated_at' => Carbon::now()]);
        }


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

        //this line is really important!!!!!!!!!!!!!!
        setlocale(LC_ALL,'en_US.UTF-8');

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
            'score' => 'numeric|between:0, 100',
        ]);

        $score = $request->get('score');
        $comment = $request->get('comment');

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
                ->update(['score' => $score, 'comment' => $comment, 'status' => 3]);
            $success_output = '<div class="alert alert-success"> 批改成功！ </div>';
        }
        $output = array(
            'error' => $error_array,
            'success' => $success_output,
            'score' => $score,
            'comment' => $comment,
        );
        echo json_encode($output);
    }


}
