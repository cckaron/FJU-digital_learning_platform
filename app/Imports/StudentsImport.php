<?php

namespace App\Imports;

use App\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        foreach ($collection as $key => $row) {
            if ($key > 0){
                if(DB::table('students')->where('users_id', $row[0])->exists()){
                    DB::tabl098e('students')
                        ->where('users_id', $row[0])
                        ->update([
                            'users_id' => $row[0],
                            'users_name' => $row[1],
                            'department' => $row[2],
                            'grade' => $row[3],
                            'class' => $row[4],
                        ]);
                } else {
                    DB::table('students')
                        ->insert([
                            'users_id' => $row[0],
                            'users_name' => $row[1],
                            'department' => $row[2],
                            'grade' => $row[3],
                            'class' => $row[4],
                        ]);
                }


                Storage::makeDirectory('public/'.$row[0]);
                Storage::disk('public')->put(
                    $row[0].'/init.txt', 'init'
                );
            }
        }
    }
}
