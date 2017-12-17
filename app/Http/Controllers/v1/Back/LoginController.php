<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\LoginExp\WrongInputExp;
use App\Models\Contract;
use App\Models\Money\ServiceMoney;
use App\Models\Services\Contract_plan;
use App\Models\Services\Service;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends ApiController
{
    use TestTrait;

    protected $sms;
    protected $auth;

    function __construct(Sms $sms, \Tymon\JWTAuth\JWTAuth $auth)
    {
        $this->sms = $sms;
        $this->auth = $auth;
    }

    public function test()
    {
//        $re = DB::select("select c1.channel_id, c1.contractc_id, c1.created_at, c1.updated_at, c1.employee_id, c1.id, c1.source, c1.status, c1.type,
//        c2.id, c2.id1, c2.id2,c2.id3,c2.id4, c2.remark, c2.t1, c2.t2, c2.updated_at,
//        c3.PM, c3.beginline, c3.company_id, c3.created_at, c3.updated_at, c3.deadline, c3.desc, c3.document, c3.id, c3.money, c3.name, c3.time,
//        c4.id, c4.name, c4.phone, c5.id, c5.name,
//        c21.id, c21.channel_apply_id, c21.id1, c21.id2, c21.id3, c21.id4,
//        c22.channel_apply_id, c22.checker_id, c22.created_at, c22.id, c22.id1, c22.id2, c22.id3, c22.id4, c22.remark, c22.t1, c22.t2, c22.updated_at,
//        c23.channel_apply_id, c23.company_id, c23.created_at, c23.device_id, c23.id, c23.id5, c23.updated_at
//        FROM channels as c1
//        LEFT JOIN channel_applies as c2 on c2.channel_id = c1.id
//        LEFT JOIN contractcs as c3 on c3.id = c1.contractc_id
//        LEFT JOIN employees as c4 on c4.id in (c1.employee_id)
//        LEFT JOIN service_sources as c5 on c5.id = c1.source
//        LEFT JOIN channel_operatives as c21 on c21.channel_apply_id = c2.id
//        LEFT JOIN channel_reals as c22 on c22.channel_apply_id = c2.id
//        LEFT JOIN channel_relations as c23 on c23.channel_apply_id = c2.id");
        $contract_id = 97;
        $model = Contract_plan::find(1)->decrement('use',2);
        return 'success';

    }

    public function test2(Request $request)
    {
        $token ="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9jdXMuYXBwL2FwaS92MS90ZXN0IiwiaWF0IjoxNTEyOTc0NTEwLCJleHAiOjE1MTI5NzgxMTAsIm5iZiI6MTUxMjk3NDUxMCwianRpIjoiMmE4QlNPNVZmTUNjQnBqMSJ9.HBXKJ-Hrn4rspySH7FaWwPvlkfOPq_ulpyaJpC8ZXmk";
        $part = explode(".", $token);
        $header = $part[0];
        $payload = $part[1];
        $signature = $part[2];
        $_payload = base64_decode($payload);
        $_payload = json_decode($_payload, true); //返回数组
        $user_id = $_payload['sub'];
        return $user_id;
    }

    /**
     * 登陆 返回 jwt_token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //fixme 未做$requestCheck
        $phone = $request->phone;
        $pass = $request->password;
        $user = User::where('phone', $phone)->first();

        if($user && Hash::check($pass, $user->password)) {
            $jwt_token = JWTAuth::fromUser($user);
            //todo 记录登陆时间
            $ip = $_SERVER['REMOTE_ADDR'];
            User::findOrFail($user['id'])->loginLogs()->create(["ip"=> $ip]);
            return $this->res(1000, $user->name . '已登陆成功', ['token' => $jwt_token]);

            //todo 短信服务, 已测试成功, 暂注释
            if($res = $this->sms->sendSms( config('sms.signature'),config('sms.AdminLogin.login'), $user->phone, [
                'customer'=>$user->name])){
                return $this->res(1000, $user->name.'已登陆成功', ['token'=>$jwt_token]);
            }
            else {
                return '登陆成功但短信发送失败';
            }
        } else {
            throw new WrongInputExp();
        }
        //fixme 三次填写错误出现验证码, 后面再做

    }

    public function check()
    {
        //todo 如果通过了中间件, 自然返回ture
        return $this->res(1000, '登陆成功');
    }
}
