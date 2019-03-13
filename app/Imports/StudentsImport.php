<?php

namespace App\Imports;

use App\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
//            return new Student([
//                'users_id' => $row[0],
//                'users_name' => $row[1],
//                'department' => $row[2],
//                'grade' => $row[3],
//                'class' => $row[4],
//            ]);

            Student::firstOrCreate([
                'users_id' => $row[0],
                'users_name' => $row[1],
                'department' => $row[2],
                'grade' => $row[3],
                'class' => $row[4],
            ]);

            Storage::makeDirectory('public/'.$row[0]);
            Storage::disk('public')->put(
                $row[0].'/init.txt', 'init'
            );
        }
    }
}
