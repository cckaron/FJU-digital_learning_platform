<?php

namespace App\Repositories;

use App\Teacher;

class TeacherRepository
{
    protected $teacher;

    /**
     * TeacherRepository constructor.
     * @param Teacher $teacher
     */
    public function __construct(Teacher $teacher){
        $this->teacher = $teacher;
    }

    public function find($id){
        return $this->teacher->find($id);
    }

    public function getProcessingCourse($teacher_id){
        $teacher = $this->teacher->where('users_id', $teacher_id)->first();

        //取得進行中的課程
        $courses = $teacher->course()
            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
            ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.status as status', 'common_courses.name as com_name')
            ->where('status', 1)
            ->get(); //in progress
        return $courses;
    }
}
