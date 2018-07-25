<?php

namespace App\Exceptions\Services;

use Exception;

//使用次数低于0，违反数学
class BelowZeroException extends Exception
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "小于0, 无法删减";
    // 自定义的错误码
    public $code = -5002; //通用类型错误号10000
}
