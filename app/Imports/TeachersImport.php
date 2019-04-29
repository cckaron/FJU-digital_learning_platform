<?php

namespace App\Imports;

use App\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TeachersImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            if ($key > 0){
                Teacher::firstOrCreate([
                    'users_id' => $row[0],
                    'users_name' => $row[1],
                ]);
            }
        }
    }
}
