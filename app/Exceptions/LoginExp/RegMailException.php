<?php

namespace App\Exceptions\LoginExp;

use Exception;

class RegMailException extends Exception
{
    // 错误具体信息
    public $msg = "验证码错误";
    // 自定义的错误码
    public $code = -2004; //通用类型错误号10000

    public $httpCode = 500;
}
