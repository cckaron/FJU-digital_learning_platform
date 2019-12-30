<?php


namespace App\Services;


use App\Repositories\StudentRepository;

class StudentService
{
    /**
     * @var StudentRepository
     */
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getCourseFinalScore($id, $course_id){
        return $this->studentRepository->getCoursePivot($id, $course_id)->pivot->final_score;
    }

    public function updateCourseFinalScore($id, $course_id, $arr){
        $this->studentRepository->updatePivot($id, $course_id, $arr);
    }

    public function getAssignmentScore($id, $assignment_id){
        return $this->studentRepository->getAssignmentPivot($id, $assignment_id)->pivot->score;
    }
}
