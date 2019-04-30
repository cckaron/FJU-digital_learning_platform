<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
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

    public function changeProfileStatus($status){
        Student::query()->update(['profileUpdated' => $status]);
        Teacher::query()->update(['profileUpdated' => $status]);
    }
}
