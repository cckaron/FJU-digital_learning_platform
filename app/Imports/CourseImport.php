<?php

namespace App\Imports;

use App\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class CourseImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row)
        {
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

            Course::create([
                'common_courses_id' => $common_course_id,
                'name' => $row[3],
                'class' => $row[4],
            ]);
        }
    }
}
