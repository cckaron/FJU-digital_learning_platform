<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'account' => 405402091,
            'id' => 405402091,
            'name' => 'Aaron',
            'email' => 'cg.workst@gmail.com',
            'password' => bcrypt('Chaosin003'), // secret
            'type' => 0,
            'remember_token' => str_random(10),
        ]);

        DB::table('users')->insert([
            'account' => 100100,
            'id' => 100100,
            'name' => 'Teacher A',
            'email' => 'aaat@gmail.com',
            'password' => bcrypt('100100'), // secret
            'type' => 3,
            'remember_token' => str_random(10),
        ]);

        DB::table('teachers')->insert([
            'users_id' => 100100,
            'users_name' => 'Teacher A',
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" =>  \Carbon\Carbon::now(),
        ]);

        DB::table('users')->insert([
            'account' => 200200,
            'id' => 200200,
            'name' => 'Teacher B',
            'email' => 'bbbt@gmail.com',
            'password' => bcrypt(str_random(10)), // secret
            'type' => 3,
            'remember_token' => str_random(10),
        ]);

        DB::table('teachers')->insert([
            'users_id' => 200200,
            'users_name' => 'Teacher B',
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" =>  \Carbon\Carbon::now(),
        ]);

        DB::table('users')->insert([
            'account' => 505102236,
            'id' => 505102236,
            'name' => 'Student A',
            'email' => 'aaa@gmail.com',
            'password' => bcrypt('505102236'), // secret
            'type' => 4,
            'remember_token' => str_random(10),
        ]);

        DB::table('students')->insert([
            'users_id' => 505102236,
            'users_name' => 'Student A',
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" =>  \Carbon\Carbon::now(),

        ]);

        DB::table('users')->insert([
            'account' => 505103399,
            'id' => 505103399,
            'name' => 'Student B',
            'email' => 'bbb@gmail.com',
            'password' => bcrypt(str_random(10)), // secret
            'type' => 4,
            'remember_token' => str_random(10),
        ]);

        DB::table('students')->insert([
            'users_id' => 505103399,
            'users_name' => 'Student B',
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" =>  \Carbon\Carbon::now(),
        ]);
    }
}
