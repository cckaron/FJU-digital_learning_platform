<?php

namespace App\Services;

use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Exception;

class TeacherService
{
    private $teacherRepository;

    /**
     * TeacherService constructor.
     * @param TeacherRepository $teacherRepository
     */
    public function __construct(TeacherRepository $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    public function findTeacher($user_id){
        try {
            $teacher = $this->teacherRepository->find($user_id);
            return $teacher;
        } catch (Exception $exception){
            return Redirect::back()->withErrors(['message', $exception->getMessage()]);
        }
    }
}
