<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Illuminate\Support\Facades\Redirect;
use Exception;

class CourseService
{
    private $courseRepository;

    /**
     * UserService constructor.
     * @param CourseRepository $courseRepository
     */
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function findCourse($course_id){
        try {
            //取得用戶資訊
            $user = $this->courseRepository->find($course_id);
            return $user;
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }


}
