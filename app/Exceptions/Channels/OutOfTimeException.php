<?php

namespace App\Exceptions\Channels;

use Exception;

class OutOfTimeException extends Exception
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "时间超出套餐限制";
    // 自定义的错误码
    public $code = -5001; //通用类型错误号10000
}
