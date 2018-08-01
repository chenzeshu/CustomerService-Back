<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 13:28
 */

namespace App\Http\Helpers;


use App\Models\Employee;

class Params
{
    const ChannelTime = 900;   //信道以15分钟, 即900秒为一个单位
    const ChannelTotalUnit = 15;   //前端规定合同新建套餐, 只能以分钟为单位, 所以后台可以用除15的方法得到total

    const NOMEAL = 9999;  //没有套餐的contract_plan_id;

    public function getQianNo()
    {
        return Employee::where('name','钱正宇')->first()->id;
    }
}