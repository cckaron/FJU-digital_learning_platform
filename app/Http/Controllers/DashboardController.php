<?php

namespace App\Http\Controllers;

use App\Course;
use App\Services\AnnouncementService;
use App\Services\AssignmentService;
use App\Services\CommonCourseService;
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
use Spatie\Valuestore\Valuestore;

class DashboardController extends Controller
{
    private $announcementService;
    private $courseService;
    private $commonCourseService;
    private $sysAnnouncementService;
    private $assignmentService;
    private $settings;


    public function __construct(AnnouncementService $announcementService, CourseService $courseService, SystemAnnouncementService $systemAnnouncementService, CommonCourseService $commonCourseService, AssignmentService $assignmentService)
    {
        $this->announcementService = $announcementService;
        $this->courseService = $courseService;
        $this->sysAnnouncementService = $systemAnnouncementService;
        $this->commonCourseService = $commonCourseService;
        $this->assignmentService = $assignmentService;

        $this->settings = Valuestore::make(storage_path('app/settings.json'));
    }

    public function get(){
        $user = Auth::user();
        $profileController = new ProfileController();

        $user = User::where('id', $user->id)->first();

        //switch role
        if ($user->type == 0) {
            $teachers = Teacher::all();
            $hasInProgressCourse = false;

            //檢查課程是否過期(直接用common_course下去檢查, 省去 query 時間)
            if ($this->settings->get("expire_checker") == true){
                $com_courses = $this->commonCourseService->get($status=1); //取得目前進行中的共同課程

                foreach($com_courses as $com_course){
                    $isDue = $this->commonCourseService->dueOrNot($com_course->id);
                    if ($isDue){
                        $this->commonCourseService->update($com_course->id, ['status' => 0]);
                    }
                }

                foreach($teachers as $teacher){
                    if($this->courseService->exist($teacher, $status=1))
                    {
                        return view('dashboard.index', [
                            'hasInProgressCourse' => true,
                            'teacher' => $teacher,
                        ]);
                    }
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

            //檢查課程是否過期
            if ($this->settings->get("expire_checker") == true) {
                $this->dueChecker($courses);
            }

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

        //獲取系統公告
        $sys_announcements = $this->sysAnnouncementService->getPaginateWithFileDetail();

        $hasInProgressCourse = $this->courseService->exist($teacher, $status=1);

        //如果有進行中的課程
        if ($hasInProgressCourse){
            //獲取課程資料
            $course = $this->courseService->findByRole($teacher);

            //檢查課程是否過期
            if ($this->settings->get("expire_checker") == true) {
                $this->dueChecker($course);
            }
        }


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

        $course = $this->courseService->findByRole($student);

        //獲取系統公告
        $sys_announcements = $this->sysAnnouncementService->getPaginateWithFileDetail();

        //如果有進行中的課程
        if ($course->count() > 0){
            //檢查課程是否過期
            if ($this->settings->get("expire_checker") == true) {
                foreach($course as $c){
                    $isDue = $this->courseService->dueOrNot($c->id);

                    if ($isDue){
                        $com_course = $this->courseService->findCommonCourse($c->id);
                        $this->commonCourseService->update($com_course->id, ['status' => 0]);
                    }
                }
            }

            //獲取課程公告 TODO 如果一個學生同時修了兩堂課, 只會顯示第一堂課的公告
            $course_first = $course->first(); //獲得 array 第一個值! 等同 $course[0]
            $announcements = $course_first->announcement()->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);
        }

        //作業
        foreach ($course as $c){
            $assignments = $this->courseService->findAssignment($c->id);

            $hashids_course = new Hashids('course_id', 6);
            $c->course_id = $hashids_course->encode($c->id);

            $c->assignment = $assignments;


            foreach($c->assignment as $assignment){
                //檢查作業是否截止
                if ($this->settings->get("expire_checker") == true) {
                    $isDue = $this->assignmentService->dueOrNot($assignment->id);
                    if ($isDue) {
                        $this->assignmentService->update($assignment->id, ['status' => 0]);
                    }
                }

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
            'courses' => $course,
        ]);
    }

    public function dueChecker($courses){
        foreach($courses as $c){
            $isDue = $this->courseService->dueOrNot($c->id);

            if ($isDue){
                $com_course = $this->courseService->findCommonCourse($c->id);
                $this->commonCourseService->update($com_course->id, ['status' => 0]);
            }

            //檢查作業是否截止
            $assignments = $this->courseService->findAssignment($c->id);
            foreach($c->assignment as $assignment){
                $isDue = $this->assignmentService->dueOrNot($assignment->id);
                if ($isDue){
                    $this->assignmentService->update($assignment->id, ['status' => 0]);
                }
            }
        }
    }
}
