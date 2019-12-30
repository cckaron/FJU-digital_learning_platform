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

    public function find($id){
        return $this->course->find($id);
    }

    public function update($id, $arr){
        $this->course->where('id', $id)->update($arr);
    }

    public function whereIn($id){
        return Course::whereIn('id', $id)->get();
    }

    public function getCommonCourse($id){
        $course = $this->course->where('id', $id)->first();
        return $course->common_course()->first();
    }

    //field
    public function getAnnouncementField($id, $field){
        $course = $this->course->where('id', $id)->first();
        return $course->announcement()->pluck('announcements.'.$field)->toArray();
    }

    public function getCommonCourseField($id, $field){
        $course = $this->course->where('id', $id)->first();
        return $course->common_course()->value($field);
    }

    public function findTeachers($ids){
        $teachers = collect();
        foreach($ids as $id){
            $teacher = $this->course->where('id', $id)->first()->teacher()->first();
            $teachers->push($teacher);
        }
        return $teachers;
    }

    public function findAssignment($id){
        $course = $this->find($id);
        return $course->assignment()
            ->select('assignments.*', 'assignments.id as assignment_id', 'assignments.name',
                'assignments.end_date', 'assignments.end_time')
            ->get();
    }

    public function getStudentPivot($id, $student_id){
        return $this->find($id)->student()->where('users_id', $student_id)->withPivot(['final_score', 'remark'])->first();
    }
}
