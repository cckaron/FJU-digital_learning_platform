<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersStudentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'account' => $row[0],  //帳號就是學號
            'id' => $row[0],
            'name' => $row[1],
            'email' => $row[0].'@mail.fju.edu.tw',
            'password' => bcrypt($row[0]), // secret  //密碼就是學號
            'type' => 4,
            'remember_token' => str_random(10),
        ]);
    }
}
