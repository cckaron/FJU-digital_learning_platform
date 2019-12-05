<?php

namespace App\Http\Controllers;

use App\Course;
use App\Services\AnnouncementService;
use App\Services\CourseService;
use App\Services\SystemAnnouncementService;
use App\Student;
use App\system_announcement;
use App\Ta;
use App\Teacher;
use App\User;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * @var AnnouncementService
     * @var CourseService
     */
    private $announcementService;
    private $courseService;
    private $sysAnnouncementService;

    public function __construct(AnnouncementService $announcementService, CourseService $courseService, SystemAnnouncementService $systemAnnouncementService)
    {
        $this->announcementService = $announcementService;
        $this->courseService = $courseService;
        $this->sysAnnouncementService = $systemAnnouncementService;
    }

    public function get(){
        $user = Auth::user();
        $profileController = new ProfileController();

        $user = User::where('id', $user->id)->first();

        //switch role
        if ($user->type == 0) {
            $teachers = Teacher::all();
            $hasInProgressCourse = false;

            foreach($teachers as $teacher){
                if($this->courseService->exist($teacher, $status=1))
                {
                    return view('dashboard.index', [
                        'hasInProgressCourse' => true,
                        'teacher' => $teacher,
                    ]);
                }
            }
            return view('dashboard.index', [
                'hasInProgressCourse' => $hasInProgressCourse,
            ]);
        } else if ($user->type == 2){
            return $this->TA();

        } else if ($user->type == 3){
            $teacher = $user->teacher()->first();

            if ($teacher->profileUpdated){
                return $this->Teacher();
            }
            return $profileController->getUpdateProfile();

        } else if ($user->type == 4){

            $student = $user->student()->first();

            if ($student->profileUpdated){
                return $this->Student();
            }
            return $profileController->getUpdateProfile();
        }
        return view('dashboard.index');
    }

    public function TA(){
        $ta = Ta::where('users_id', Auth::user()->id)->first();
        $hasInProgressCourse = $this->courseService->exist($ta, $status=1);

        //取得系統公告
        $sys_announcements = $this->sysAnnouncementService->getPaginateWithFileDetail();

        //判斷該TA是否當學期有課程, 有則回傳該TA指導老師的資訊
        if($hasInProgressCourse)
        {
            $courses = $this->courseService->findByRole($ta); //取得 ta 的課程
            $courses = $courses->unique("name"); // 老師可能會帶很多課程, 只須取不重複的即可(因為只需要老師的資訊), 這樣也能減輕下面 findTeachers 的 loading

            $teachers = $this->courseService->findTeachers($courses);

            return view('dashboard.taIndex', [
                'hasInProgressCourse' => true,
                'teachers' => $teachers,
                'sys_announcements' => $sys_announcements,
            ]);
        } else {
            return view('dashboard.taIndex', [
                'hasInProgressCourse' => false,
                'sys_announcements' => $sys_announcements,
            ]);
        }
    }

    public function Teacher(){
        $teacher = Teacher::where('users_id', Auth::user()->id)->first();
        $hasInProgressCourse = $this->courseService->exist($teacher, $status=1);

        //取得系統公告
        $sys_announcements = $this->sysAnnouncementService->getPaginateWithFileDetail();

        return view('dashboard.teacherIndex', [
            'hasInProgressCourse' => $hasInProgressCourse,
            'sys_announcements' => $sys_announcements,
        ]);
    }

    public function Student(){
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
        $sys_announcements = $this->sysAnnouncementService->getPaginateWithFileDetail();

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
