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
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$arr1 = ['已签约', '未签约'];
$factory->define(\App\Models\Company::class, function (Faker $faker, $arr1) {
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'profession'=> rand(0,10),
        'type' => '已签约'
    ];
});
$arr2 = ['offline', 'online'];
$factory->define(\App\Models\Employee::class, function (Faker $faker, $arr2) {
    return [
        'name' => $faker->name,
        'openid' => $faker->unique()->creditCardNumber,
        'email' => $faker->unique()->safeEmail,
        'phone'=> rand(0,99999),
        'status' => 'online'
    ];
});