<?php

namespace App\Exceptions\Services;

use Exception;

class TooMuchUseException extends Exception
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "套餐使用达到上限";
    // 自定义的错误码
    public $code = -5002; //通用类型错误号10000
}
