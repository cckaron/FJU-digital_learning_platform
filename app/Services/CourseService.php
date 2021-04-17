<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Exception;

class CourseService implements eventService
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

    public function find($id){
        try {
            return $this->courseRepository->find($id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function update($id, $arr){
        $this->courseRepository->update($id, $arr);
    }

    public function dueOrNot($id)
    {
        $course = $this->findCommonCourse($id);
        $date = Carbon::parse($course->end_date);
        return Carbon::today()->gt($date) ? true : false; //if now time is greater than due date, then it is due.
    }

    public function exist($role, $status=1){
        return $role->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status')
            ->where('status', $status)
            ->exists();
    }

    public function getAllOpenCourse(){
        return $this->courseRepository->getAllOpenCourse();
    }

    public function findByRole($role, $status=1){
        if ($status == 3){ //get all courses (include ongoing and ended)
            return $role->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status', 'common_courses.year as year', 'common_courses.semester as semester')
                ->get();
        } else {
            return $role->course()
                ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                ->select('courses.*', 'common_courses.name as common_course_name', 'common_courses.status as status', 'common_courses.year as year', 'common_courses.semester as semester')
                ->where('status', $status)
                ->get();
        }
    }

    public function findTeachers($courses){
        return $this->courseRepository->findTeachers($courses->pluck('id'));
    }

    public function findCommonCourse($id){
        try {
            return $this->courseRepository->getCommonCourse($id);
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }

    public function findAssignment($id){
        return $this->courseRepository->findAssignment($id);
    }

    public function getStudentFinalScore($id, $student_id){
        return $this->courseRepository->getStudentPivot($id, $student_id)->pivot->final_score;
    }


}
