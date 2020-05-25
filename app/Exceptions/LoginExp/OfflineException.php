<?php

namespace App\Exceptions\LoginExp;

use App\Exceptions\BaseException;

class OfflineException extends \Exception
{
    // 错误具体信息
    public $msg = "此账号已被下线, 请联系管理员";
    // 自定义的错误码
    public $code = -1001; //通用类型错误号10000

    public $httpCode = 404;
}
