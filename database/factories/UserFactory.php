<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'account' => str_random(9),
        'id' => rand(100000000, 999999999),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(str_random(10)), // secret
        'type' => 0,
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\User::class, 'teacher', function ($faker) {
    return [
        'account' => str_random(9),
        'id' => rand(100000000, 999999999),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(str_random(10)), // secret
        'type' => 3,
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\User::class, 'student', function ($faker) {
    return [
        'account' => str_random(9),
        'id' => rand(100000000, 999999999),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(str_random(10)), // secret
        'type' => 4,
        'remember_token' => str_random(10),
    ];
});
