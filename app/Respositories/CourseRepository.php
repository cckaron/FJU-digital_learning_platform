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

    public function whereIn($courses_id){
        return Course::whereIn('id', $courses_id)->get();
    }

    public function getAnnouncementField($course_id, $field){
        $course = $this->course->where('id', $course_id)->first();
        $announcementField = $course->announcement()->pluck('announcements.'.$field)->toArray();
        return $announcementField;
    }

    public function getCommonCourseField($course_id, $field){
        $course = $this->course->where('id', $course_id)->first();
        $commoncourseField = $course->common_course()->value($field);
        return $commoncourseField;
    }
}
