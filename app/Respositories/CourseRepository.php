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
}
