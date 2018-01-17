<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;
class ChannelsUtilsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        //带宽表
        DB::table('channel_info1s')->insert([
            ['name' => '512k'],
            ['name' => '2m']
        ]);

        //站类型表
        DB::table('channel_info2s')->insert([
            ['name' => '车载站'],
            ['name' => '地面站'],
            ['name' => '便携站']
        ]);

        //通信卫星表
        DB::table('channel_info3s')->insert([
            ['name' => '亚卫5号'],
            ['name' => '亚卫6号'],
            ['name' => '亚卫9号']
        ]);

        //频率表
        DB::table('channel_info4s')->insert([
            ['name' => '1MHz'],
            ['name' => '2MHz']
        ]);

        //极化表
        DB::table('channel_info5s')->insert([
            ['name' => '水平'],
            ['name' => '垂直']
        ]);

        //网络类型表
        DB::table('channel_info6s')->insert([
            ['name' => '点对点'],
            ['name' => '星状网']
        ]);

        //套餐表
        DB::table('plans')->insert([
           ['name'=>$faker->name, 't1'=>'点对点', 't2'=>'512k', 't3'=>'独享'],
           ['name'=>$faker->name,  't1'=>'点对点', 't2'=>'512k', 't3'=>'共享'],
           ['name'=>$faker->name,  't1'=>'点对点', 't2'=>'2m',   't3'=>'独享'],
           ['name'=>$faker->name,  't1'=>'点对点', 't2'=>'2m',   't3'=>'共享'],
           ['name'=>$faker->name,  't1'=>'星状网', 't2'=>'512k', 't3'=>'独享'],
           ['name'=>$faker->name,  't1'=>'星状网', 't2'=>'512k', 't3'=>'共享'],
           ['name'=>$faker->name,  't1'=>'星状网', 't2'=>'2m',   't3'=>'独享'],
           ['name'=>$faker->name,  't1'=>'星状网', 't2'=>'2m',   't3'=>'共享'],
        ]);
    }
}
