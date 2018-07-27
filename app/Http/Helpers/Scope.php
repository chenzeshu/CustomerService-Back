<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 8:42
 */

namespace App\Http\Helpers;


class Scope
{
    const ORDINARY = 8;  //一般权限者
    const TEMP_CONTRACT_SERVICE_MANAGER = 16;  //临时合同的服务单的审批者, 如信道临时合同为钱正宇
}