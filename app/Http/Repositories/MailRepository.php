<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28
 * Time: 8:57
 */

namespace App\Http\Repositories;


use App\Services\Sms;

class MailRepository
{
    /**
     *  向用户发送验证码
     */
    public function sendCode($phoneNumber, $code)
    {
        return (new Sms())->sendSms(env('SIGN_NAME'), "SMS_92115199", $phoneNumber,['code'=>$code]);
    }
    
    /**
     * 通知用户注册通过
     */
    public function sendRegMsg($phoneNumber, $name)
    {
        $time = date('Y-m-d H:i:s',time());
        return (new Sms())->sendSms(env('SIGN_NAME'), "SMS_120125166", $phoneNumber,['name'=>$name, 'time'=>$time]);
    }
    /**
     * 通知用户注册未通过
     */
    public function sendRegFailMsg($phoneNumber, $name)
    {
        return (new Sms())->sendSms(env('SIGN_NAME'), "SMS_120115188", $phoneNumber,['name'=>$name]);
    }

    /**
     * 发送警报
     * @param $phoneNumber
     * @param $data   ['name' => 联系人姓名， 'device_name' => 设备名称， 'problem_desc'=>问题描述，'four00tel' => env(400电话)]
     * @return \App\Services\stdClass
     */
    public function sendReportMsg($phoneNumber, $data)
    {
        return (new Sms())->sendSms(env('SIGN_NAME'), "SMS_152284845", $phoneNumber, $data);
    }
}