<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
//        $assignments = Course::with('assignment')
//            ->join('assignments', 'courses.id', 'assignments.courses_id')
//            ->join('common_courses', 'courses.common_courses_id', 'common_courses.id')
//            ->select('courses.name as course_name',
//                'common_courses.name as common_course_name',
//                'common_courses.year',
//                'common_courses.semester',
//                'common_courses.status as common_course_status',
//                'assignments.id as assignment_id',
//                'assignments.name as assignment_name',
//                'assignments.status as assignment_status',
//                'assignments.percentage as assignment_percentage',
//                'assignments.start_date',
//                'assignments.end_date',
//                'assignments.updated_at')
//            ->where('common_courses.status', 1)
//            ->where('common_courses.name', '產業創新(八)')
////            ->whereIn('common_courses.name', ['產業創新(二)', '產業創新(四)', '產業創新(六)'])
//            ->get();
//
//        foreach($assignments as $assignment){
//            DB::table('assignments')
//                ->where('id', $assignment->assignment_id)
//                ->update(['end_date' => '2019/5/31', 'end_time' => '20:20']);

//        }
        DB::table('users')
            ->where('id', '=','051266')
            ->update([
                'password' => bcrypt('051266')
            ]);

        return "success";
    }

    public function changeProfileStatus($status){
//        DB::table('users')
//            ->update(['phone' => null]);
//        Student::query()->update(['profileUpdated' => $status, 'occupation' => null]);
//
//        Teacher::query()->update(['profileUpdated' => $status]);

        //把所有學生的密碼設為和帳號相同
//        $students = Student::all();
//        foreach($students as $student){
//            DB::table('users')
//                ->where('id', $student->users_id)
//                ->update(['password' => bcrypt($student->users_id)]);
//        }

        DB::table('users')
            ->where('type', 3)
            ->update(['password' => bcrypt('051266')]);
    }


}
