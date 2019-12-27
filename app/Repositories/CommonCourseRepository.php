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

    public function get($status){
        return $this->common_course->where('status', $status)->get();
    }

    public function all(){
        return $this->common_course->all();
    }

    public function update($id, $arr){
        $this->common_course->where('id', $id)->update($arr);
    }
}
