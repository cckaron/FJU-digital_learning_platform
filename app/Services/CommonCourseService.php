<?php

namespace App\Services;

use App\Course;
use App\Repositories\CommonCourseRepository;
use App\Ta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Exception;

class CommonCourseService implements eventService
{
    private $commonCourseRepository;


    /**
     * UserService constructor.
     * @param $commonCourseRepository $courseRepository
     */
    public function __construct(CommonCourseRepository $commonCourseRepository)
    {
        $this->commonCourseRepository = $commonCourseRepository;
    }

    public function find($id){
        try {
            return $this->commonCourseRepository->find($id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function check($course_id){}


}
