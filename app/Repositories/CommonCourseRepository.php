<?php

namespace App\Repositories;

use App\common_course;

class CommonCourseRepository
{
    protected $common_course;

    public function __construct(common_course $common_course){
        $this->common_course = $common_course;
    }

    public function find($id){
        return $this->common_course->find($id);

    }
}
