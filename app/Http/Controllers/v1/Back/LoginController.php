<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\LoginExp\WrongInputExp;
use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CompanyCollection;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Services\Service;
use App\Models\Utils\Profession;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class LoginController extends Controller
{
    use ReturnTrait, TestTrait;

    protected $sms;
    protected $auth;

    function __construct(Sms $sms, \Tymon\JWTAuth\JWTAuth $auth)
    {
        $this->sms = $sms;
        $this->auth = $auth;
    }

    public function test()
    {
        $services = Service::orderBy('id', 'desc')->offset(10)->limit(10)->with('contract')->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->man = $item->man == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->man})");
                $item->customer = $item->customer == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->customer})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                $item->company = Company::where('id', $item['contract']['company_id'])->get(['id', 'name'])[0];
                return $item;
            })
            ->toArray();
        return $services;
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
