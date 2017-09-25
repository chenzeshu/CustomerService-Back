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

$factory->define(\App\Models\Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'profession'=> rand(0,10),
        'type' => '已签约'
    ];
});

$factory->define(\App\Models\Employee::class, function (Faker $faker) {
    $arr = ['offline', 'online'];
    return [
        'name' => $faker->name,
        'openid' => $faker->unique()->creditCardNumber,
        'email' => $faker->unique()->safeEmail,
        'phone'=> rand(0,99999),
        'status' => 'online'
    ];
});

$factory->define(\App\Models\Contract::class, function (Faker $faker){
    $type2 = ['销售', '客服', '临时'];
    return [
        "contract_id" => 'F'.date('Ymd', time()).rand(0,1000),
        "type1"=>rand(0,5),
        "type2"=>$faker->randomElement($type2),
        "PM"=>rand(0,100),
        "TM"=>rand(0,100),
        "time1" => $faker->dateTime(),
        "time2" =>$faker->dateTime(),
        "money" => $faker->randomFloat(2,0, 10000000),
        "coor" => rand(0,10)
    ];
});

$factory->define(\App\Models\Services\Service::class, function (Faker $faker){
    $status = ['待审核','拒绝', '已派单', '已完成', '申请完成', '申述中'];
    $charge = ['收费', "未收费"];
    return [
        "service_id" => 'F'.date('Ymd', time()).rand(0,1000),
        "status" => $faker->randomElement($status),
        "source" => rand(0,5),
        "type"=> rand(0,5),
        "man" => rand(0,100),
        "customer" => rand(0,100),
        "charge_if" => $faker->randomElement($charge),
        "charge" => $faker->randomFloat(2, 0, 1000),
        "time1" => $faker->dateTime(),
        "time2" => $faker->dateTime(),
        'day_sum'=>rand(0,10),
    ];
});

$factory->define(\App\Models\Channels\Channel::class, function (Faker $faker){
    $status = ['待审核','运营调配', '已完成', '拒绝'];
    return [
        "channel_id"=> 'X'.date('Ymd', time()).rand(0,1000),
        "status"=>  $faker->randomElement($status),
        "source" => rand(0,5),
    ];
});

