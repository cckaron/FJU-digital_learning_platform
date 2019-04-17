<?php

namespace App\Imports;

use App\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class GradeImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @throws \Exception
     */
    public function collection(Collection $collection)
    {
        $teacher_id = Auth::user()->id;
        $assignments = array();
        $splitTitle = null;
        $splitName = null;

        foreach ($collection as $key=>$row){
            if ($key == 0){
                $splitTitle = explode('_', $row[0], 4);
            } else if ($key == 1){
                for ($i=4; $i<count($row); $i++) {
                    if ($row[$i] == '最終成績'){
                        array_push($assignments, $row[$i]);
                    } else {
                        $splitName = explode('(', $row[$i], 2);
                        array_push($assignments, $splitName[0]);
                    }
                }
            } else if ($key > 1) {

                $student = Student::where('users_id', $row[0])->first();

                for ($i=4; $i<count($row); $i++) {
                    if ($assignments[$i-4] == '最終成績'){
                        $student_course = null;
                        $student_course = $student->course()
                            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                            ->join('teacher_course', 'courses.id', '=', 'courses.id')
                            ->where('common_courses.status', 1) //課程進行中
                            ->where('common_courses.year', $splitTitle[0]) //年度為excel中標訂的年度
                            ->where('common_courses.semester', $splitTitle[1]) // 學期為 excel中標訂的學期
                            ->where('teacher_course.teachers_id', $teacher_id) //課程老師是匯入excel的老師
                            ->where('courses.name', $splitTitle[2]) //課程名稱是excel中標訂的名稱
                            ->select('student_course.students_id', 'student_course.courses_id')
                            ->first();

                        Log::info($student_course);
                        foreach($student_course as $student_course_pivot){
                            DB::table('student_course')
                                ->where('students_id', $student_course->students_id)
                                ->where('courses_id', $student_course->courses_id)
                                ->update(['final_score' => $row[$i]]);
                        }
                    } else {
                        $student_assignment = null;
                        $student_assignment = $student->assignment()
                            ->withPivot('id')
                            ->join('courses', 'courses.id', '=', 'assignments.courses_id')
                            ->join('teacher_course', 'courses.id', '=', 'assignments.courses_id')
                            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                            ->where('common_courses.status', 1) //課程進行中
                            ->where('common_courses.year', $splitTitle[0]) //年度為excel中標訂的年度
                            ->where('common_courses.semester', $splitTitle[1]) // 學期為 excel中標訂的學期
                            ->where('teacher_course.teachers_id', $teacher_id) //課程老師是匯入excel的老師
                            ->where('assignments.name', $assignments[$i-4])
                            ->select('assignments.*', 'courses.common_courses_id as common_course_id', 'common_courses.status as common_course_status')
                            ->first();

                        foreach($student_assignment as $student_assignment_pivot){
                            DB::table('student_assignment')
                                ->where('id', $student_assignment->pivot->id)
                                ->update(['score' => $row[$i]]);
                        }
                    }


//                    Log::info(print_r("id is:".$student_assignment->id.", score is".$row[$i],true));
                }
            }
        }
//        Log::info(print_r($assignments,true));
    }
}
