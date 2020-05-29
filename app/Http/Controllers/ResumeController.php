<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Services\StudentService;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class resumeController extends Controller
{
    /**
     * @var StudentService
     * @var CourseService
     */
    private $studentService;
    private $courseService;

    public function __construct(StudentService $studentService, CourseService $courseService)
    {
        $this->studentService = $studentService;
        $this->courseService = $courseService;
    }

    public function preview(){
//        $user = Auth::user();
//        $student = Student::where('users_id', $user->id)->first();

        return view('resume.main', [
//            'student' => $student,
        ]);
    }

    public function test(){
        $user = Auth::user();
        $student = Student::where('users_id', $user->id)->first();
        $st_courses = $this->courseService->findByRole($student, 0);

        foreach($st_courses as $course){
            $assignments = $this->courseService->findAssignment($course->id);

            foreach($assignments as $assignment){
                if ($assignment->name == "書面報告Word"){
                    $student_assignment = DB::table('student_assignment')
                        ->where('students_id', $student->users_id)
                        ->where('assignments_id', $assignment->id)
                        ->first();

                    $course->topic = trim($student_assignment->title);
                }
            }
        }

        return view('resume.test', [
            'student' => $student,
            'courses' => $st_courses
        ]);
    }
}
