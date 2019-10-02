<?php

namespace App\Imports;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;


class UsersTeacherImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $key => $row) {
            if ($key > 0) {
                if (DB::table('users')->where('id', $row[0])->exists()) {
                    Log::info("exists");
                    Log::info($row[0]);
                    DB::table('users')
                        ->where('id', (string)$row[0])
                        ->update([
                            'name' => (string)$row[1],
                            'type' => 3,
                            'updated_at' => Carbon::now()
                        ]);
                } else {
                    Log::info("not exists");
                    Log::info($row[0]);
                    DB::table('users')
                        ->insert([
                            'account' => $row[0],  //帳號就是學號
                            'id' => $row[0],
                            'name' => $row[1],
                            'email' => $row[0] . '@mail.fju.edu.tw',
                            'password' => bcrypt($row[0]), // secret  //密碼就是學號
                            'type' => 3,
                            'remember_token' => str_random(10),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                }
            }
        }
    }
}
