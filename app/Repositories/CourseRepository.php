<?php

namespace App\Repositories;

use App\Course;
use App\course_announcement;


class CourseRepository
{
    protected $course;

    /**
     * CourseRepository constructor.
     * @param Course $course
     */
    public function __construct(Course $course){
        $this->course = $course;
    }

    public function find($course_id){
        return $this->course->find($course_id);
    }

    public function whereIn($courses_id){
        return Course::whereIn('id', $courses_id)->get();
    }

    public function getAnnouncementField($course_id, $field){
        $course = $this->course->where('id', $course_id)->first();
        return $course->announcement()->pluck('announcements.'.$field)->toArray();
    }

    public function getCommonCourseField($course_id, $field){
        $course = $this->course->where('id', $course_id)->first();
        return $course->common_course()->value($field);
    }

    public function findTeachersByCourse($courses_id){
        $teachers = collect();
        foreach($courses_id as $course_id){
            $teacher = $this->course->where('id', $course_id)->first()->teacher()->first();
            $teachers->push($teacher);
        }
        return $teachers;
    }
}
