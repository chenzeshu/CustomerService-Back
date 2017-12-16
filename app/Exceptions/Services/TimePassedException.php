<?php

namespace App\Exceptions\Services;

use App\Exceptions\BaseException;

class TimePassedException extends BaseException
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "合同过期";
    // 自定义的错误码
    public $code = -5001; //通用类型错误号10000
}
