<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\LoginExp\WrongInputExp;
use App\Models\Channels\Channel_duty;
use App\Models\Channels\Channel_relation;
use App\Models\Employee;
use App\Models\Utils\Plan;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends ApiController
{
    use  TestTrait;

    protected $sms;
    protected $auth;

    function __construct(Sms $sms, \Tymon\JWTAuth\JWTAuth $auth)
    {
        $this->sms = $sms;
        $this->auth = $auth;
    }

    public function test()
    {
        $page = 1;
        $pageSize = 10;
        $name = "k";
        $begin = ( $page -1 ) * $pageSize;
        $model = Channel_duty::offset($begin)
            ->limit($pageSize)
            ->orderBy('id', 'desc')
            ->get()
            ->each(function ($item){
                $item->checker = $item->employee_id == null ? null : DB::select("select `id`, `name` from employees where id = {$item->employee_id} limit 1");
//                $item->checker = $item->checker[0];
            })
            ->toArray();

return $model;
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
