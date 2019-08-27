<?php

namespace App\Services;

use App\Announcement;
use App\Course;
use App\Repositories\AnnouncementRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Redirect;

class AnnouncementService
{
    private $announcementRepository;
    private $teacherRepository;

    /**
     * UserService constructor.
     * @param AnnouncementRepository $announcementRepository
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(AnnouncementRepository $announcementRepository, TeacherRepository $teacherRepository)
    {
        $this->announcementRepository = $announcementRepository;
        $this->teacherRepository = $teacherRepository;
    }

    public function getByUserRole($user)
    {
        //確認user type
        switch ($user->type){
            case 2: //TA
                break;
            case 3: //teacher
                //取得進行中的課程
                $courses = $this->teacherRepository->getProcessingCourse($user->id);

                //TODO 將以下代碼實現 Repository 和 Services
                //取得所有課程中的公告id, 篩選出不重複的id
                $announcements_id = array();
                foreach($courses as $course){
                    $announcement_id = $course->announcement()->pluck('announcements.id')->toArray();
                    foreach ($announcement_id as $id){
                        array_push($announcements_id, $id);
                    }
                }
                $announcements_id = array_unique($announcements_id);

                //反向查詢
                $announcements = Announcement::whereIn('id', $announcements_id)->orderBy('priority')->orderBy('updated_at', 'desc')->paginate(5);

                foreach($announcements as $announcement){
                    $courses_id = $announcement->course()->pluck('courses.id');

                    $courses = Course::whereIn('id', $courses_id)->get();

                    //查詢共同課程的名稱
                    $common_courses_name = collect();
                    foreach($courses as $course){
                        $common_course_name = $course->common_course()->value('name');
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
