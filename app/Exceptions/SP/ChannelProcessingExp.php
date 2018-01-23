<?php

namespace App\Exceptions\SP;

use App\Exceptions\BaseException;

class ChannelProcessingExp extends BaseException
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "之前申请未完结";  //上次申请仍处于审核或运营调配中，没有生成channel_real
    // 自定义的错误码
    public $code = -70002; //通用类型错误号10000
}
