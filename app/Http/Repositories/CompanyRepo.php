<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 9:45
 */

namespace App\Http\Repositories;

use Faker\Generator as Faker;

class CompanyRepo
{
    /**
     *  为新公司新建临时信道合同
     */
    public function createTempContractc($data, Faker $faker){
        $temp = [
            "contract_id" => 'X'.date('Ymd', time()).rand(0,1000),
            "money" => $faker->randomFloat(2,2000000, 6000000),
            "PM"=>rand(1,100),  //以后改成钱正宇
            'name'=>"临时合同",
            "time" => $faker->date('Y-m-d H:i:s'),
            "beginline" =>$faker->date('Y-m-d H:i:s'),
            "deadline" =>$faker->date('Y-m-d H:i:s'),
        ];
        $data->contract_cs()->create($temp);
    }

    /**
     *  为新公司新建临时服务合同
     */
    public function createTempContract($data, Faker $faker){
        $type2 = ['销售', '客服', '临时'];
        return [
            "contract_id" => 'F'.date('Ymd', time()).rand(0,1000),
            'name'=>"临时合同",
            "money" => $faker->randomFloat(2,500000, 10000000),
            "type1"=>rand(1,3),
            "type2"=>$faker->randomElement($type2),
            "PM"=>rand(1,100).','.rand(1,100),  //以后改成临时合同负责人
            "time1" => $faker->date('Y-m-d H:i:s'),
            "time2" =>$faker->date('Y-m-d H:i:s'),
            "coor" => rand(1,10),
            "document" => rand(1,100).','.rand(1,100),
        ];
        $data->contracts()->create($temp);
    }
}