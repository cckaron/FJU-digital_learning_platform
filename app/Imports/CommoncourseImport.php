<?php

namespace App\Imports;

use App\common_course;
use App\Course;
use App\student_course;
use App\teacher_course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class CommoncourseImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @throws \Exception
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row){
            if ($key > 0){
                //add common_course
                $commoncourse = DB::table('common_courses')
                    ->where('year', $row[0])
                    ->where('semester', $row[1])
                    ->where('name', $row[2])
                    ->exists();

                if (!$commoncourse){
                    common_course::create([
                        'year' => $row[0],
                        'semester' => $row[1],
                        'name' => $row[2],
                        'start_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['6'])->format('Y/m/d'),
                        'end_date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['7'])->format('Y/m/d')
                    ]);
                }

                //add course
                //row filter
                if ($row[4] == "甲"){
                    $row[4] = 1;
                } elseif($row[4] == "乙"){
                    $row[4] = 2;
                } elseif($row[4] == "不分班"){
                    $row[4] = 3;
                }


                $common_course_id = DB::table('common_courses')
                    ->where('year', $row[0])
                    ->where('semester', $row[1])
                    ->where('name', $row[2])
                    ->value('id');

                Course::firstOrCreate([
                    'common_courses_id' => $common_course_id,
                    'name' => $row[3],
                    'class' => $row[4],
                ]);

                //add student_course
                $course_id = DB::table('courses')
                    ->join('common_courses', 'common_courses.id', '=', 'courses.common_courses_id')
                    ->select('courses.id', 'courses.name', 'courses.common_courses_id', 'common_courses.year', 'common_courses.semester', 'common_courses.name')
                    ->where('common_courses.year',  $row[0])
                    ->where('common_courses.semester', $row[1])
                    ->where('courses.name', $row[3])
                    ->where('common_courses.name', $row[2])
                    ->value('id');


                Log::info(print_r($course_id, true));
                Log::info(print_r($row[8],true));

                student_course::
                firstOrCreate([
                    'students_id' => $row[8],
                    'courses_id' => $course_id,
                ]);


                //add teacher_course
                $teacher_name = mb_substr($row[5], 0, 3, "utf-8");
                Log::info(print_r($teacher_name,true));

                $teacher_id = DB::table('teachers')
                    ->where('users_name', $teacher_name)
                    ->value('users_id');

                if (!DB::table('teachers')
                    ->where('users_name', $teacher_name)
                    ->exists()){
                    Log::info(print_r("不存在",true));

                }

                teacher_course::firstOrCreate([
                    'teachers_id' => $teacher_id,
                    'courses_id' => $course_id,
                ]);
            }

        }

    }
}
