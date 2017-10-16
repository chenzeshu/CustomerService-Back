<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\LoginExp\WrongInputExp;
use App\Http\Controllers\Controller;
use App\Http\Resources\Company\CompanyCollection;
use App\Models\Company;
use App\Models\Utils\Profession;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use ReturnTrait, TestTrait;

    protected $sms;

    function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }

    public function test()
    {
        $page = 2;
        $pageSize = 10;
        $begin = ($page -1 ) * $pageSize;

        $data = Company::offset($begin)->limit($pageSize)->get();
        $pros = Profession::all()->toArray();
        foreach ( $data as $company){
            foreach ($pros as $pro){
                if($pro['id'] == $company->profession){
                    $company->profession = $pro['name'];
                }else{
                    $company->profession = "其他行业";
                }
            }
        }
        $total = ceil(Company::count() / $pageSize);
        $data= [
            'data'=> $data,
            'total'=> $total
        ];
        return $this->res('2000', 200, $data);
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
//            if($res = $this->sms->sendSms( config('sms.signature'),config('sms.AdminLogin.login'), $user->phone, [
//                'customer'=>$user->name])){
//                return $this->res(1000, $user->name.'已登陆成功', ['token'=>$jwt_token]);
//            }
//            else {
//                return '登陆成功但短信发送失败';
//            }
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
