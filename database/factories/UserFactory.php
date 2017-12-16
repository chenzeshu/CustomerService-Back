<?php

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
$faker = Faker\Factory::create();


$factory->define(App\User::class, function () use ($faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->freeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Models\Company::class, function () use ($faker) {
    $pros = ['电力','环保','水利','安监','消防'];
    return [
        'name' => $faker->company,
        'address' => $faker->address,
        'profession'=> $faker->randomElement($pros),
        'type' => '已签约'
    ];
});

$factory->define(\App\Models\Employee::class, function () use ($faker) {
    $arr = ['offline', 'online', '离职'];
    return [
        'name' => $faker->unique()->name,
        'openid' => $faker->unique()->creditCardNumber,
        'email' => $faker->unique()->freeEmail,
        'phone'=> $faker->unique()->phoneNumber,
        'status' => $faker->randomElement($arr)
    ];
});
//服务合同
$factory->define(\App\Models\Contract::class, function () use ($faker){
    $type2 = ['销售', '客服', '临时'];
    return [
        "contract_id" => 'F'.date('Ymd', time()).rand(0,1000),
        'name'=>$faker->domainName,
        "money" => $faker->randomFloat(2,500000, 10000000),
        "type1"=>rand(1,3),
        "type2"=>$faker->randomElement($type2),
        "PM"=>rand(1,100).','.rand(1,100),
        "TM"=>rand(1,100).','.rand(1,100),
        "time1" => $faker->date('Y-m-d H:i:s'),
        "time2" =>$faker->date('Y-m-d H:i:s'),
        "time3" =>$faker->date('Y-m-d H:i:s'),
        "coor" => rand(1,10),
        "document" => rand(1,100).','.rand(1,100),
    ];
});
//服务合同挂钩套餐
$factory->define(\App\Models\Services\Contract_plan::class, function () use ($faker){
   return [
       'plan_id' => rand(1,10),
       'total' => rand(5, 10),  //总次数
       'use' => rand(1,5), //使用次数
       'desc'=>$faker->text(30),
   ];
});
//服务合同到款一览
$factory->define(\App\Models\Money\ServiceMoney::class, function () use ($faker){
    $type = ['不分次', '分次付款'];
    $finish = ['未结清', '结清'];
    return [
        "type" =>  $faker->randomElement($type),
        "finish" =>  $faker->randomElement($finish),
        "num" => rand(1, 4),
        "t1" =>$faker->date('Y-m-d H:i:s'),
        "t2" =>$faker->date('Y-m-d H:i:s'),
        'checker_id' => rand(1,4)
    ];
});
//信道合同到款细节
$factory->define(\App\Models\Money\ServiceMoneyDetail::class, function () use ($faker){
    return [
        "money" => $faker->randomFloat(2,0, 1000000),
        "t1" =>$faker->date('Y-m-d H:i:s'),
        "t2" =>$faker->date('Y-m-d H:i:s'),
    ];
});

$factory->define(\App\Models\Contractc::class, function () use ($faker){
    return [
        "contract_id" => 'X'.date('Ymd', time()).rand(0,1000),
        "money" => $faker->randomFloat(2,2000000, 6000000),
        "PM"=>rand(1,100),
        'name'=>$faker->domainName,
        "time" => $faker->date('Y-m-d H:i:s'),
        "beginline" =>$faker->date('Y-m-d H:i:s'),
        "deadline" =>$faker->date('Y-m-d H:i:s'),
    ];
});

//信道合同到款情况
$factory->define(\App\Models\Money\ChannelMoney::class, function () use ($faker){
    $type = ['不分次', '分次付款'];
    $finish = ['未结清', '结清'];
    return [
        "type" =>  $faker->randomElement($type),
        "finish" =>  $faker->randomElement($finish),
        "num" => rand(1, 4),
        "t1" =>$faker->date('Y-m-d H:i:s'),
        "t2" =>$faker->date('Y-m-d H:i:s'),
        'checker_id' => rand(1,4)
    ];
});
//到款细节
$factory->define(\App\Models\Money\ChannelMoneyDetail::class, function () use ($faker){
    return [
        "money" => $faker->randomFloat(2,0, 10000000),
        "t1" =>$faker->date('Y-m-d H:i:s'),
        "t2" =>$faker->date('Y-m-d H:i:s'),
    ];
});

//用户选择的套餐
$factory->define(\App\Models\Channels\Channel_plan::class, function () use ($faker){
    $status = ['用完', '正常'];
   return [
        'plan' => rand(1,7),
        'full_time' =>ceil($faker->numberBetween(0, 1500)/15)*15,
        'flag' => $faker->randomElement($status),
   ];
});

//用户套餐使用记录
$factory->define(\App\Models\Channels\Channel_detail::class, function () use ($faker){
    return [
        'time' =>ceil($faker->numberBetween(0, 120)/15)*15,
    ];
});

//服务单
$factory->define(\App\Models\Services\Service::class, function () use ($faker){
    $status = ['待审核','拒绝', '待派单', '已派单', '已完成', '申请完成', '申述中'];
    $charge = ['收费', "不收费"];
    $charge_flag = ['到款', "未到款"];
    return [
        "service_id" => 'F'.date('Ymd', time()).rand(0,1000),
        "status" => $faker->randomElement($status),
        "source" => rand(1,4),
        "type"=> rand(1,5),
        "refer_man" => rand(1,100),
        "man" => rand(1,100),
        "customer" => rand(1,100),
        "visit" => rand(1,100),
        "charge_if" => $faker->randomElement($charge),
        "charge_flag" => $faker->randomElement($charge_flag),
        "charge" => $faker->randomFloat(2, 0, 1000),
        "time1" => $faker->date('Y-m-d H:i:s'),
        "time2" => $faker->date('Y-m-d H:i:s'),
        'day_sum'=>rand(0,10),
    ];
});

$factory->define(\App\Models\Services\Visit::class, function () use ($faker){
    $deal = ['待解决','已解决','未解决'];
    return [
      "service_id" => rand(0,100),
      "visitor" => rand(1,100),
      "result_deal" => $faker->randomElement($deal),
      "result_rating" => rand(0,4),
      "result_visit" => rand(0,4),
      "time" => $faker->date('Y-m-d H:i:s'),
    ];
});

//信道服务单, 暂时不填充
$factory->define(\App\Models\Channels\Channel::class, function () use ($faker){
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

//信道服务单申请
$factory->define(\App\Models\Channels\Channel_apply::class, function () use ($faker){
    return [
        "id1" => rand(1,8),
        "id2" => rand(1,2),
        "id3" => rand(1,2),
        "id4" => rand(1,2),
        't1'=>$faker->date('Y-m-d H:i:s'),
        't2'=>$faker->date('Y-m-d H:i:s'),
        'remark'=>$faker->name,
    ];
});

//信道服务单节点
$factory->define(\App\Models\Channels\Channel_relation::class, function () use ($faker){
   return [
       'company_id' => rand(1,50),
       'device_id' => rand(1,150),  //fixme 按理 , 只能筛选出单位id下关联的device, 此处为了方便填充先这样了
       'id5' => rand(1,2),
   ];
});

//信道服务单运营调配
$factory->define(\App\Models\Channels\Channel_operative::class, function () use ($faker){
    return [
        "id1" => rand(1,8),
        "id2" => rand(1,2),
        "id3" => rand(1,2),
        "id4" => rand(1,2),
        't1'=>$faker->date('Y-m-d H:i:s'),
        't2'=>$faker->date('Y-m-d H:i:s'),
        'remark'=>$faker->name,
    ];
});


//信道服务单实际
$factory->define(\App\Models\Channels\Channel_real::class, function () use ($faker){
    return [
        "id1" => rand(1,8),
        "id2" => rand(1,2),
        "id3" => rand(1,2),
        "id4" => rand(1,2),
        'checker_id' => rand(1,100),  //fixme 实际只能从中网员工里选, faker阶段先随便写
        't1'=>$faker->date('Y-m-d H:i:s'),
        't2'=>$faker->date('Y-m-d H:i:s'),
        'remark'=>$faker->name,
    ];
});

$factory->define(\App\Models\Utils\Device::class, function () use ($faker){
    $type = ['ad','非ad'];
    $pro = [1,2,3,4,5];
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

$factory->define(\App\Models\Doc::class, function () use ($faker){
    $arr = ['public/documents/2017-10/haha.doc', 'public/documents/2017-11/beauty.doc', 'public/documents/2017-12/beautys.doc'];
   return [
      "name" => $faker->creditCardType.".". $faker->fileExtension,
      "path" => $faker->randomElement($arr)
   ];
});


/*******************************测试*********************/