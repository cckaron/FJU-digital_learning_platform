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

    public function exist($role, $status=1){
        return $role->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', $status)
            ->exists();
    }

    public function findByRole($role, $status=1){
        return $role->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', $status)
            ->get();
    }

    public function findTeachers($courses){
        return $this->courseRepository->findTeachersByCourse($courses->pluck('id'));
    }


}
