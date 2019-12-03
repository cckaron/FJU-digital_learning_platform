<?php

namespace App\Services;

use App\Course;
use App\Repositories\CourseRepository;
use App\Ta;
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
            return $this->courseRepository->find($course_id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function hasInProgressCourse($role){

        return $role->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->exists();
    }

    public function findTACourse(Ta $ta){
        return $ta->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', 1)
            ->get();
    }

    public function findTeacher($courses){
        return $this->courseRepository->findTeacherByCourse($courses->pluck('id'));
    }


}
