<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15
 * Time: 15:50
 */

namespace App\Exceptions;

use Psy\Exception\RuntimeException;

class BaseException extends RuntimeException
{
    // Http状态码
    public $httpCode = 500;
    // 错误具体信息
    public $msg = "参数错误";
    // 自定义的错误码
    public $code = 10000; //通用类型错误号10000
}