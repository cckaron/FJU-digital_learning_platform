<?php

namespace App\Services;

use App\Announcement;
use App\Course;
use App\Repositories\AnnouncementsRepository;
use App\Repositories\CourseRepository;
use App\Repositories\TaRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Redirect;

class AnnouncementService
{
    private $announcementRepository;
    private $teacherRepository;
    private $courseRepository;
    private $taRepository;

    /**
     * UserService constructor.
     * @param AnnouncementsRepository $announcementRepository
     * @param TeacherRepository $teacherRepository
     * @param CourseRepository $courseRepository
     * @param TaRepository $taRepository
     */
    public function __construct(AnnouncementsRepository $announcementRepository, TeacherRepository $teacherRepository, CourseRepository $courseRepository, TaRepository $taRepository)
    {
        $this->announcementRepository = $announcementRepository;
        $this->teacherRepository = $teacherRepository;
        $this->courseRepository = $courseRepository;
        $this->taRepository = $taRepository;
    }

    public function get($user)
    {
        //確認user type
        switch ($user->type){
            case 2: //TA
                //取得 TA 該學期負責的 Course
                $courses = $this->taRepository->getProcessingCourse($user->id);
                //TODO 還沒寫完?
                break;

            case 3: //teacher
                //TODO 感覺可以結合 concrete 的概念，把取 teacher 資料的另外寫在 teacher service，然後建一個 AnnouncementConcrete 套用 Service
                //取得 teacher 進行中的課程
                $courses = $this->teacherRepository->getProcessingCourse($user->id);

                //取得 teacher 所有課程中的公告id, 篩選出不重複的
                $announcements_id = collect();
                foreach($courses as $course){
                    $announcement_id = $this->courseRepository->getAnnouncementField($course->id, 'id');

                    //push to collection
                    $announcements_id->push($announcement_id);
                }
                //turning 2d array to 1d array in collection
                $announcements_id = $announcements_id->collapse();
                //return all unique id
                $announcements_id = $announcements_id->unique();

                //取得公告
                $announcements = $this->announcementRepository->getPaginate($announcements_id);
                foreach($announcements as $announcement){
                    $courses_id = $this->announcementRepository->getCourseField($announcement->id, 'id');
                    $courses = $this->courseRepository->whereIn($courses_id);

                    //取得共同課程的名稱
                    $common_courses_name = collect();
                    foreach($courses as $course){
                        $common_course_name = $this->courseRepository->getCommonCourseField($course->id, 'name');
                        $common_courses_name->push($common_course_name);
                    }
                    $announcement->common_courses_name = $common_courses_name;
                }
                return $announcements;

            case 4: //student
                break;

            default:
                return Redirect::back()->withErrors(['message', 'Illegal Role!']);
        }

    }
}
