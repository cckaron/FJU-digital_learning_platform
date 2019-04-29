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
            } else if ($key == 1){ //找出所有作業名稱，加到 assignments array 裡面
                Log::info('Now is row'.$key);
                Log::info('row length is '.count($row));

                for ($i=4; $i< count($row); $i++){
                    // 判斷Excel中 第一行的作業名稱
                    if ($row[$i] == null){ // 不用做事
                    } else if ($row[$i] == '最終成績'){ // 加入"最終成績"
                        array_push($assignments, $row[$i]);
                    } else { // 加入括弧前的字 例如 A4海報(10%) 就只會擷取 A4海報 四個字
                        $splitName = explode('(', $row[$i], 2);
                        array_push($assignments, $splitName[0]);
                    }
                }
                Log::info($assignments);
            } else if ($key > 1) {

                $student = Student::where('users_id', $row[0])->first();

                for ($i=0; $i<count($assignments); $i++) {
                    if ($assignments[$i] == '最終成績'){
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

                        DB::table('student_course')
                            ->where('students_id', $student_course->students_id)
                            ->where('courses_id', $student_course->courses_id)
                            ->update(['final_score' => $row[$i+4]]);
                    } else {
                        $student_assignment = $student->assignment()
                            ->withPivot('id')
                            ->join('courses', 'courses.id', '=', 'assignments.courses_id')
                            ->join('teacher_course', 'courses.id', '=', 'assignments.courses_id')
                            ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                            ->where('common_courses.status', 1) //課程進行中
                            ->where('common_courses.year', $splitTitle[0]) //年度為excel中標訂的年度
                            ->where('common_courses.semester', $splitTitle[1]) // 學期為 excel中標訂的學期
                            ->where('teacher_course.teachers_id', $teacher_id) //課程老師是匯入excel的老師
                            ->where('assignments.name', $assignments[$i])
                            ->first();

                        Log::info('split');
                        Log::info($student);
                        Log::info($student_assignment);
                        $id = $student_assignment->pivot->id;

                        Log::info($student_assignment->pivot->id);

                        DB::table('student_assignment')
                            ->where('id', $id)
                            ->update(['score' => $row[$i+4], 'status' => 3]); //狀態:已批改


                    }
                }
            }
        }
    }
}
