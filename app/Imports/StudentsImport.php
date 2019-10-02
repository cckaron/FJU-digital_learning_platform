<?php

namespace App\Imports;

use App\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                if(DB::table('students')->where('users_id', (string)$row[0])->exists()){
                    Log::info("Exists");
                    DB::table('students')
                        ->where('users_id', (string)$row[0])
                        ->update([
                            'users_name' => $row[1], //有可能改名
                            'department' => $row[2], //類推
                            'grade' => $row[3],
                            'class' => $row[4],
                            'updated_at' => Carbon::now()
                        ]);
                } else {
                    Log::info("not exists");
                    DB::table('students')
                        ->insert([
                            'users_id' => $row[0],
                            'users_name' => $row[1],
                            'department' => $row[2],
                            'grade' => $row[3],
                            'class' => $row[4],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                    Storage::makeDirectory('public/'.$row[0]);
                    Storage::disk('public')->put(
                        $row[0].'/init.txt', 'init'
                    );
                }
            }
        }
    }
}
