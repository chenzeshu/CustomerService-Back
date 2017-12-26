<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/21
 * Time: 9:00
 */

namespace App\Exceptions\ScopeExp;


use App\Exceptions\BaseException;

class ScopeExp extends BaseException
{
    // 错误具体信息
    public $msg = "你的权限不足";
    // 自定义的错误码
    public $code = -4001; //通用类型错误号10000

    public $httpCode = 404;
}