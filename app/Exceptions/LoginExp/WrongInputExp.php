<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21
 * Time: 9:00
 */

namespace App\Exceptions\LoginExp;


use App\Exceptions\BaseException;

class WrongInputExp extends BaseException
{
    // 错误具体信息
    public $msg = "手机号或密码填写错误";
    // 自定义的错误码
    public $code = -1001; //通用类型错误号10000
}