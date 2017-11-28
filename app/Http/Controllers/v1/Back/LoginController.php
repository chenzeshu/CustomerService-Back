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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $data = DB::table('channel_relations')->where('device_id', 1)->get();
        if($data){
            return $data;
        }else {
            echo "123";
        } 
    }

    public function test2()
    {
    	//todo
        $page = 1;
        $pageSize = 10;
        $begin = ( $page -1 ) * $pageSize;
        $cons = Channel::orderBy('id', 'desc')->where('status', "!=", '待审核')->offset($begin)->limit($pageSize)
            ->with(['contractc',
                'channel_applys.channel_relations.company',
                'channel_applys.channel_relations.device',
                'channel_applys.channel_operative.tongxin',
                'channel_applys.channel_operative.jihua',
                'channel_applys.channel_operative.pinlv',
                'channel_applys.channel_operative.plan',
                'channel_applys.channel_real.tongxin',
                'channel_applys.channel_real.jihua',
                'channel_applys.channel_real.pinlv',
                'channel_applys.channel_real.checker'])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->customer = $item->employee_id == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->employee_id})");
                $item->source_info = $item->source == null ? null : DB::select("select `id`, `name` from service_sources where id = {$item->source}");
                return $item;
            })
            ->toArray();
        $sources = Service_source::all()->toArray();
        $pinlvs = Channel_info4::all()->toArray();
        $tongxins = Channel_info3::all()->toArray();
        $jihuas = Channel_info5::all()->toArray();
        $plans = Plan::all()->toArray();
        $zhantypes =Channel_info2::all()->toArray();

        $total = Channel::where('status', "!=", '待审核')->count();
        $data = [
            'data' => $cons,
            'total' => $total,
            'sources' => $sources,
            'pinlvs' => $pinlvs,
            'jihuas' => $jihuas,
            'tongxins' => $tongxins,
            'plans' => $plans,
            'zhantypes'=>$zhantypes,
        ];
        return $this->res(200, '信道服务单', $data);
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
