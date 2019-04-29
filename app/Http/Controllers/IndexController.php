<?php

namespace App\Http\Controllers;

use App\Course;
use App\Student;
use App\Teacher;
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
        $sys_announcements = collect();

        $course = $student->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
            ->where('status', 1); //in progress

        //課程公告
        if ($course->exists()){
            $course = $course->first();
            $announcements = $course->announcement()->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);
        }
//        $course = $student->course()
//            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
//            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
//            ->where('status', 1) //in progress
//            ->first(); //just showing one class, even though student has two class(it should not be happened)





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


        return view('dashboard.studentIndex', [
            'sys_announcements' => $sys_announcements,
            'announcements' => $announcements,
            'course' => $course,
        ]);
    }
}
