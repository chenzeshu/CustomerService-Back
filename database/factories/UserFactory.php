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
    $pros = ['电力','环保','水利','安监','消防'];
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'profession'=> $faker->randomElement($pros),
        'type' => '已签约'
    ];
});

$factory->define(\App\Models\Employee::class, function (Faker $faker) {
    $arr = ['offline', 'online', '离职'];
    return [
        'name' => $faker->name,
        'openid' => $faker->unique()->creditCardNumber,
        'email' => $faker->unique()->safeEmail,
        'phone'=> $faker->unique()->phoneNumber,
        'status' => $faker->randomElement($arr)
    ];
});

$factory->define(\App\Models\Contract::class, function (Faker $faker){
    $type2 = ['销售', '客服', '临时'];
    return [
        "contract_id" => 'F'.date('Ymd', time()).rand(0,1000),
        'name'=>$faker->name,
        "type1"=>rand(0,5),
        "type2"=>$faker->randomElement($type2),
        "PM"=>rand(0,100).','.rand(0,100),
        "TM"=>rand(0,100).','.rand(0,100),
        "time1" => $faker->date('Y-m-d H:i:s'),
        "time2" =>$faker->date('Y-m-d H:i:s'),
        "money" => $faker->randomFloat(2,0, 10000000),
        "coor" => rand(0,10),
        "document" => rand(0,100).','.rand(0,100),
    ];
});

$factory->define(\App\Models\Contractc::class, function (Faker $faker){
    return [
        "contract_id" => 'X'.date('Ymd', time()).rand(0,1000),
        "PM"=>rand(0,100),
        'name'=>$faker->name,
        "time" => $faker->date('Y-m-d H:i:s'),
        "beginline" =>$faker->date('Y-m-d H:i:s'),
        "deadline" =>$faker->date('Y-m-d H:i:s'),
        "money" => $faker->randomFloat(2,0, 10000000),
    ];
});

//用户选择的套餐
$factory->define(\App\Models\Channels\Channel_plan::class, function (Faker $faker){
    $status = ['用完', '正常'];
   return [
        'plan' => rand(0,7),
        'full_time' =>ceil($faker->numberBetween(0, 1500)/15)*15,
        'flag' => $faker->randomElement($status),
   ];
});

//用户套餐使用记录
$factory->define(\App\Models\Channels\Channel_detail::class, function (Faker $faker){
    return [
        'time' =>ceil($faker->numberBetween(0, 120)/15)*15,
    ];
});

$factory->define(\App\Models\Services\Service::class, function (Faker $faker){
    $status = ['待审核','拒绝', '已派单', '已完成', '申请完成', '申述中'];
    $charge = ['收费', "不收费"];
    $charge_flag = ['到款', "未到款"];
    return [
        "service_id" => 'F'.date('Ymd', time()).rand(0,1000),
        "status" => $faker->randomElement($status),
        "source" => rand(1,4),
        "type"=> rand(1,5),
        "man" => rand(0,100),
        "customer" => rand(0,100),
        "visit" => rand(0,100),
        "charge_if" => $faker->randomElement($charge),
        "charge_flag" => $faker->randomElement($charge_flag),
        "charge" => $faker->randomFloat(2, 0, 1000),
        "time1" => $faker->date('Y-m-d H:i:s'),
        "time2" => $faker->date('Y-m-d H:i:s'),
        'day_sum'=>rand(0,10),
    ];
});

$factory->define(\App\Models\Services\Visit::class, function (Faker $faker){
    $deal = ['待解决','已解决','未解决'];
    return [
      "service_id" => rand(0,100),
      "visitor" => rand(0,100),
      "result_deal" => $faker->randomElement($deal),
      "result_rating" => rand(0,4),
      "result_visit" => rand(0,4),
      "time" => $faker->date('Y-m-d H:i:s'),
    ];
});


//信道服务单, 暂时不填充
$factory->define(\App\Models\Channels\Channel::class, function (Faker $faker){
    $status = ['待审核','运营调配', '已完成', '拒绝'];
    $type = ['内部用星', '外部用星'];
    return [
        "channel_id"=> 'X'.date('Ymd', time()).rand(0,1000),
        "employee_id" => rand(3,100),
        "status"=>  $faker->randomElement($status),
        "source" => rand(1,4),
        "type"=>$faker->randomElement($type),
    ];
});

$factory->define(\App\Models\Utils\Device::class, function (Faker $faker){
    $type = ['ad','非ad'];
    $pro = [0,1,2,3,4,5];
    $status = ['停用', '重要', '一般', '自用', '损坏', '专项处理'];
    return [
        "type"=>  $faker->randomElement($type),
        'id5'=>rand(1,2),
        'device_id'=> $faker->md5,
        'ip' => $faker->ipv4,
        'sn' => $faker->uuid,
        'profession_id'=> $faker->randomElement($pro),
        'status' => $faker->randomElement($status),
        'aerial' => 1,
        'pa' => 2,
        'lnb' => 'lnb型号',
        'built_at'=>$faker->date('Y-m-d H:i:s'),
    ];
});

$factory->define(\App\Models\Doc::class, function (Faker $faker){
    $arr = ['public/documents/2017-10/haha.doc', 'public/documents/2017-11/beauty.doc', 'public/documents/2017-12/beautys.doc'];
   return [
      "name" => $faker->name,
      "path" => $faker->randomElement($arr)
   ];
});

