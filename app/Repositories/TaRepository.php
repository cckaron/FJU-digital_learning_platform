<?php

namespace App\Repositories;

use App\Ta;
use App\User;

class TaRepository
{
    private $ta;

    /**
     * TaRepository constructor.
     * @param Ta $ta
     */
    public function __construct(Ta $ta){
        $this->ta = $ta;
    }

    public function find($id){
        return $this->ta->find($id);
    }

    public function getProcessingCourse($ta_id){
        $ta = $this->ta->where('users_id', $ta_id)->first();

        //取得進行中的課程
        $courses = $ta->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
            ->where('status', config('constants.course.status.process'))
            ->get(); //in progress
        return $courses;
    }
}
