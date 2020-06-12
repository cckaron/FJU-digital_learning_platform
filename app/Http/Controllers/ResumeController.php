<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use App\Services\StudentService;
use App\Student;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function test($student_id){
        $student = Student::where('users_id', $student_id)->first();
        $st_courses = $this->courseService->findByRole($student, 0);

        $st_courses = $this->resortByChineseNumber($st_courses);

        foreach($st_courses as $course){
            $assignments = $this->courseService->findAssignment($course->id);

            foreach($assignments as $assignment){
                if ($assignment->name == "A4海報"){
                    $student_assignment = DB::table('student_assignment')
                        ->where('students_id', $student->users_id)
                        ->where('assignments_id', $assignment->id)
                        ->first();

                    $course->topic = trim($student_assignment->title);
                }
            }
        }

//        return PDF::setOptions([
//            'isHtml5ParserEnabled' => true,
//            'isRemoteEnabled' => true,
//            'defaultFont' => 'HanyiSentyTang'
//        ])
//            ->setPaper("A4", 'portrait')
//            ->loadView('resume.test', [
//                'student' => $student,
//                'courses' => $st_courses,
//            ])->stream();

        return view('resume.test', [
            'student' => $student,
            'courses' => $st_courses,
        ]);
    }

    public function resortByChineseNumber(Collection $sources){
        $ans = collect();
        $sequence = [
            '產業創新(一)', '產業創新(二)', '產業創新(三)', '產業創新(四)',
            '產業創新(五)', '產業創新(六)', '產業創新(七)', '產業創新(八)'
        ];

        $flag = 0;

        //從 "產業創新(一)" 開始搜尋到 "產業創新(八)"
        while ($flag < 8){
            //如果 sources 內存在此 flag, return 該 index
            $result = $sources->search(function ($item, $key) use ($sequence, $flag){
                return $item->common_course_name == $sequence[$flag];
            });

            //如果存在該值, 將它 push 進 ans
            if ($result !== false){
                $ans->push($sources[$result]);
            }

            $flag ++;
        }

        return $ans;
    }
}
