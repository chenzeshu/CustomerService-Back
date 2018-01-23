<?php

namespace App\Exceptions\SP;

use App\Exceptions\BaseException;

class ChannelNoCheckerExp extends BaseException
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "上回无负责人";  //没有负责人
    // 自定义的错误码
    public $code = -70001; //通用类型错误号10000
}
