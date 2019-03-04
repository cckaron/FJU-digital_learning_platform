<?php

namespace App\Imports;

use App\common_course;
use Maatwebsite\Excel\Concerns\ToModel;

class CommoncourseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new common_course([
            'year' => $row[0],
            'semester' => $row[1],
            'name' => $row[2],
            'start_date' => $row[3],
            'end_date' => $row[4],
        ]);
    }
}
