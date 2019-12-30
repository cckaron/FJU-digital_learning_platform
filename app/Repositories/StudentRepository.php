<?php


namespace App\Repositories;


use App\Services\eventService;
use App\Student;

class StudentRepository{
    /**
     * @var Student
     */
    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function find($id){
        return $this->student->where('users_id', $id)->first();
    }

    public function update($id, $arr){
        return $this->student->where('id', $id)->update($arr);
    }

    public function updatePivot($id, $course_id, $arr){
        $this->find($id)->course()->updateExistingPivot($course_id, $arr);
    }

    public function getCoursePivot($id, $course_id){
        return $this->find($id)->course()->where('id', $course_id)->withPivot(['final_score', 'remark'])->first();
    }

    public function getAssignmentPivot($id, $assignment_id){
        return $this->find($id)->assignment()->where('assignments.id', $assignment_id)->withPivot(['score', 'title', 'comment', 'status'])->first();
    }
}
