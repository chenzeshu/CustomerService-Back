<?php

namespace App\Exceptions\Services;

use Exception;

class NeedPositiveNumberException extends Exception
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "请提交正数";
    // 自定义的错误码
    public $code = -5003; //通用类型错误号10000
}
