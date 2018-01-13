<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 13:28
 */

namespace App\Http\Helpers;


class Params
{
    const ChannelTime = 900;   //信道以15分钟, 即900秒为一个单位
    const ChannelTotalUnit = 15;   //前端规定合同新建套餐, 只能以分钟为单位, 所以后台可以用除15的方法得到total
}