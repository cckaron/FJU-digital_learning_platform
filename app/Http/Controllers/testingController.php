<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class testingController extends Controller
{
    public function changeAssignmentStatus($common_course_status, $assignment_status){
        //改作業狀態
        $assignments = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
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
            ->where('common_courses.status', $common_course_status)
            ->get();

        foreach($assignments as $assignment){
            DB::table('assignments')
                ->where('id', $assignment->assignment_id)
                ->update(['status' => $assignment_status]);
        }
    }

    public function changeAssignmentEndDate(){
        //改作業狀態
        $assignments = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
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
            ->where('common_courses.status', 1)
//            ->whereIn('common_courses.name', ['產業創新(二)', '產業創新(四)', '產業創新(六)'])
            ->get();
//
        foreach($assignments as $assignment){
            DB::table('assignments')
                ->where('id', $assignment->assignment_id)
                ->update(['end_date' => '2020/1/2', 'end_time' => '23:59']);
        }

        $assignments = Course::with('assignment')
            ->join('assignments', 'courses.id', 'assignments.courses_id')
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
            ->where('common_courses.status', 1)
            ->where('courses.name', '洪郁雯老師')
            ->get();

        foreach($assignments as $assignment){
            DB::table('assignments')
                ->where('id', $assignment->assignment_id)
                ->update(['end_date' => '2020/1/4', 'end_time' => '23:59']);
        }

        return "success";
    }

    public function changeProfileStatus($status){
        Student::query()->update(['profileUpdated' => $status]);

        Teacher::query()->update(['profileUpdated' => $status]);

        //把所有學生的密碼設為和帳號相同
//        $students = Student::all();
//        foreach($students as $student){
//            DB::table('users')
//                ->where('id', '504151266')
//                ->update(['password' => bcrypt('504151266')]);
//        }
//
//        DB::table('users')
//            ->where('type', 3)
//            ->update(['password' => bcrypt('051266')]);
    }

    public function manualLogin($number){
        $user = User::where('email','=',$number.'@mail.fju.edu.tw')->first();
        Auth::login($user);

        return redirect()->route('dashboard.get');

    }


    public function exportStudentData(){
        $user = Auth::user();
        $teachers = Teacher::all();
        //get all assignments of teacher
        $assignments = collect();
        $student_assignments = collect();

        $assignments_id = array();

        $student_assignment_assignments_id = array();
        $student_assignment_status = array();

        $common_courses_name = array();
        $common_courses_status = array();

        $assignments_name = array();
        $assignments_status = array();

        $student_assignments_id = array();

        $student_ids = array();
        $scores = array();
        $titles = array();
        $updated_at = array();
        $comments = array();
        $makeupDate = array();

        $file_names = array();
        $file_urls = array();

        //取得進行中的課程
        $courses = Course::
            join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();

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
        foreach($assignments as $assignment){
            array_push($assignments_id, $assignment->pluck('id')->toArray());
        }

        //flatten the 2-dimensional array to 1-dimensional array
        $assignments_id = call_user_func_array('array_merge', $assignments_id);


        //get all student_assignments
        foreach($assignments_id as $assignment_id){
            $student_assignment = DB::table('student_assignment')
                ->where('assignments_id', $assignment_id)
                ->get();
            $student_assignments->push($student_assignment);
        }

        //pluck assignment id from student_assignment
        foreach($student_assignments as $student_assignment){
            array_push($student_assignment_assignments_id, $student_assignment->pluck('assignments_id')->toArray());
            array_push($student_assignment_status, $student_assignment->pluck('status')->toArray());

        }

        //flatten the 2-dimensional array to 1-dimensional array
        $student_assignment_assignments_id = call_user_func_array('array_merge', $student_assignment_assignments_id);
        $student_assignment_status = call_user_func_array('array_merge', $student_assignment_status);

        //get the common course name and status
        foreach($student_assignment_assignments_id as $assignment_id){
            $assignment = Assignment::where('id', $assignment_id)->first();
            $common_course = $assignment->course()->first()->common_course()->first();
            $common_course_name = $common_course->name;
            $common_course_status = $common_course->status;

            array_push($common_courses_name, $common_course_name);
            array_push($common_courses_status, $common_course_status);

        }

        //get assignment name

        foreach($student_assignment_assignments_id as $assignment_id){
            $assignment = Assignment::where('id', $assignment_id)->first();
            $assignment_name = $assignment->name;
            $assignment_status = $assignment->status;
            array_push($assignments_name, $assignment_name);
            array_push($assignments_status, $assignment_status);
        }


        //pluck student_assignment_id
        foreach($student_assignments as $student_assignment){
            array_push($student_assignments_id, $student_assignment->pluck('id')->toArray());
        }

        //flatten the 2-dimensional array to 1-dimensional array
        $student_assignments_id = call_user_func_array('array_merge', $student_assignments_id);


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

        for ($i=0; $i< count($student_assignments_id); $i++){

            $names = array("hi");
            $urls = array("hello");

//            $folder_path = storage_path().'/app/public/'.$student_ids[$i].'/'.$student_assignment_assignments_id[$i];
//
//            if (!File::exists($folder_path) ){
//                File::makeDirectory($folder_path, $mode = 0777, true, true);
//            }
//
//            setlocale(LC_ALL,'en_US.UTF-8');
//
//            Log::info($folder_path);
//            $filesInFolder = File::files($folder_path);
//            Log::info($filesInFolder);
//            foreach($filesInFolder as $path) {
//                $file = pathinfo($path);
//
//                if($file['filename'] != 'blob'){ //空的檔案
//                    array_push($names, $file['filename'].'.'.$file['extension']) ;
//                    array_push($urls, ['public', $student_ids[$i], $student_assignment_assignments_id[$i], $file['filename'].'.'.$file['extension']]);
//                }
//            }

            array_push($file_names, $names);
            array_push($file_urls, $urls);
        }

        return view('assignment.correctAssignment', [
            'user' => $user,
            'teacher' => $teachers[0],
            'student_assignments' => $student_assignments,
            'student_assignments_id' => $student_assignments_id,
            'student_ids' => $student_ids,
            'scores' => $scores,
            'titles' => $titles,
            'makeUpDate' => $makeupDate,
            'updated_at' => $updated_at,
            'student_names' => $student_names,

            'teachers' => $teachers,
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

            'file_names' => $file_names,
            'file_urls' => $file_urls
        ]);
    }
}
