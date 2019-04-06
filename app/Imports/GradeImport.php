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
                for ($i=2; $i<count($row); $i++) {

                    $splitName = explode('(', $row[$i], 2);
                    array_push($assignments, $splitName[0]);
                }
            } else if ($key > 1) {

                $student = Student::where('users_id', $row[0])->first();

                for ($i=2; $i<count($row); $i++) {
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
                        ->where('assignments.name', $assignments[$i-2])
                        ->select('assignments.*', 'courses.common_courses_id as common_course_id', 'common_courses.status as common_course_status')
                        ->first();

                    foreach($student_assignment as $student_assignment_pivot){
                        DB::table('student_assignment')
                            ->where('id', $student_assignment->pivot->id)
                            ->update(['score' => $row[$i]]);
                    }

//                    Log::info(print_r("id is:".$student_assignment->id.", score is".$row[$i],true));
                }
            }



//            $assignment_name = array();
//
//            if ($key == 2){
//                //擷取要匯入的課程名稱
//                for ($i=2; $i<count($row); $i++) {
////                    $assignment = $teacher->assignment()->where('name', $row[$i])->first();
////                    $assignments->push($assignment);
//                    array_push($assignment_name, $row[$i]);
//                    //找出 assignment name 對應的 assignment id
//                    DB::table('assignments')
//                        ->where('')
//                }
//
//
//
//            } else if ($key > 2){
//                foreach ($assignment_name as $key2 => $assignment) {
//                    DB::table('student_assignments')
//                        ->where('students_id', $row[0])
//                        ->where('assignments_id', $assignment->id)
//                        ->update(['score' => $row[$key2+2]]);
//                }
//            }
        }
//        Log::info(print_r($assignments,true));
    }
}
