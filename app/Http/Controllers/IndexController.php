<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function getTeacherIndex(){
        $teacher = Teacher::where('users_id', Auth::user()->id)->first();

        //系統公告
        $sys_announcements = DB::table('system_announcement')
            ->orderBy('priority')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        //use ->paginate(), so don't need ->get()

        //put file info in collect()
        foreach($sys_announcements as $sys_announcement) {

            $fileNames = array();

            $folder_path = storage_path() . '/app/public/sys_announcement/' . $sys_announcement->id;

            if (!File::exists($folder_path)) {
                File::makeDirectory($folder_path, $mode = 0777, true, true);
            }

            setlocale(LC_ALL, 'en_US.UTF-8');

            $filesInFolder = File::files($folder_path);
            foreach ($filesInFolder as $path) {
                $file = pathinfo($path);

                if ($file['filename'] != 'blob') { //空的檔案
                    array_push($fileNames, $file['filename'] . '.' . $file['extension']);
                }
            }

            $sys_announcement->fileNames = $fileNames;
        }

        $hasInProgressCourse = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->exists();

        return view('dashboard.teacherIndex', [
            'hasInProgressCourse' => $hasInProgressCourse,
            'sys_announcements' => $sys_announcements,

        ]);
    }

    public function getStudentIndex(){
        $student_id = Auth::user()->id;
        $student = Student::where('users_id', $student_id)->first();

        $course = null;
        $announcements = collect();

        $course = $student->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
            ->where('status', 1); //in progress

        //課程公告
        if ($course->exists()){
            $course = $course->first();
            $announcements = $course->announcement()->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);
        }

        //系統公告
        $sys_announcements = DB::table('system_announcement')
            ->orderBy('priority')
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
        //use ->paginate(), so don't need ->get()

        //put file info in collect()
        foreach($sys_announcements as $sys_announcement) {

            $fileNames = array();

            $folder_path = storage_path() . '/app/public/sys_announcement/' . $sys_announcement->id;

            if (!File::exists($folder_path)) {
                File::makeDirectory($folder_path, $mode = 0777, true, true);
            }

            setlocale(LC_ALL, 'en_US.UTF-8');

            $filesInFolder = File::files($folder_path);
            foreach ($filesInFolder as $path) {
                $file = pathinfo($path);

                if ($file['filename'] != 'blob') { //空的檔案
                    array_push($fileNames, $file['filename'] . '.' . $file['extension']);
                }
            }

            $sys_announcement->fileNames = $fileNames;
        }

        //作業
        $student = Student::where('users_id', $student_id)->first();
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
                    ->where('users_id', $student_id)
                    ->first();
            }
        }


        return view('dashboard.studentIndex', [
            'sys_announcements' => $sys_announcements,
            'announcements' => $announcements,
            'course' => $course, //這個是公告用的
            'courses' => $courses, //這個是作業用的
        ]);
    }
}
