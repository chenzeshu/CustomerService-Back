<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\LoginExp\WrongInputExp;
use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CompanyCollection;
use App\Models\Channels\Channel_info2;
use App\Models\Channels\Channel_real;
use App\Models\Channels\Channel_relation;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Doc;
use App\Models\Services\Service;
use App\Models\Utils\Device;
use App\Models\Utils\Profession;
use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_info4;
use App\Models\Channels\Channel_info5;
use App\Models\Channels\Channel;
use App\Models\Utils\Plan;
use App\Models\Utils\Service_source;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

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
        Cache::forget('contractcs');
    }

    public function test2()
    {

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
