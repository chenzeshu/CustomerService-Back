<?php

namespace App\Http\Controllers\v1\Back;

use App\Dao\ServiceDAO;
use App\Exceptions\BaseException;
use App\Exceptions\LoginExp\OfflineException;
use App\Exceptions\LoginExp\WrongInputExp;
use App\Models\Employee;
use App\Models\Employee_waiting;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\CurlFuncs;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends ApiController
{
    use TestTrait, CurlFuncs;

    protected $sms;
    protected $auth;
    protected $name = [];

    function __construct(Sms $sms, \Tymon\JWTAuth\JWTAuth $auth)
    {
        $this->sms = $sms;
        $this->auth = $auth;
    }

    public function myeach($foos, $foosCallback)
    {
        $this->name[$foos] = $foosCallback->bindTo($this, __CLASS__);
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
//        $user = User::where('phone', 18502557106)->first();
//        $jwt_token = JWTAuth::fromUser($user, ['scope' => $user['scope']]);//不使用JWTAuth::attemp,  为了记录登陆信息
//        return $jwt_token;
//        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9";
//        $token = hash_hmac("sha256", $token, "secret");

//        DB::update('update check_phones set status = 0 where id = 1');
//        return ;
//        $phoneNumber = "18502557106";
//        $code = "31".rand(1000,9999);
//        Check_phone::create([
//            'phone' => $phoneNumber,
//            'code' => $code,
//            'expire_at' => date('Y-m-d h:i:s', time()+300)
//        ]);


//        $data = DB::select("SELECT s.service_id, s.status, s.charge_if, s.time1, s.time2 ,s.man,
//        c.name, c2.name as customer, c3.name as type
//        FROM services as s
//        LEFT JOIN employees as c on c.id in (s.man)
//        LEFT JOIN employees as c2 on c2.id = s.customer
//        LEFT JOIN service_types as c3 on c3.id = s.type
//        where find_in_set('$emp_id', s.man )
//        ORDER BY s.time1 desc
//        LIMIT 0,5");
//        if(count($data) > 1){
//            collect($data)->map(function ($d){
//                if(strpos($d->man, ',')){
//                    $arr = DB::select("select name from employees where id in (".$d->man.")");
//                    $d->name = collect($arr)->implode('name', ",");
//                }
//            });
//        }
//        else{
//            if(strpos($data[0]->man, ',')){
//                $arr = DB::select("select name from employees where id in (".$data[0]->man.")");
//                $data[0]->name = collect($arr)->implode('name', ",");
//            }
//        }
        $emp_id = 27;
        $page = 1;
        $pageSize = 100;
        $begin = ($page - 1) * $pageSize;
        $data = DB::select("SELECT s.service_id, s.status, s.charge_if, s.time1, s.time2 ,s.man, s.customer as customer_id,
        c.name, c2.name as customer, c3.name as type
        FROM services as s 
        LEFT JOIN employees as c on c.id in (s.man) 
        LEFT JOIN employees as c2 on c2.id = s.customer
        LEFT JOIN service_types as c3 on c3.id = s.type
        where find_in_set('$emp_id', s.man) 
        ORDER BY s.time1 desc
        LIMIT $begin, $pageSize");

        $len = count($data);
        if($len >= 1){
            collect($data)->map(function ($d){
                $company = Employee::with('company')->where('id', $d->customer_id)->get();
                $d->company = collect($company[0]['company'])->except(['created_at', 'updated_at', 'profession']);
                if(strpos($d->man, ',')){
                    $arr = DB::select("select name from employees where id in (".$d->man.")");

                    $d->name = collect($arr)->implode('name', ",");

                }
            });
        }

        return $data;
    }

    public function test2(Request $request)
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzY29wZSI6MTUsInN1YiI6MSwiaXNzIjoiaHR0cDovL2N1cy5hcHAvYXBpL3YxL2NvbXBhbnkvcGFnZS8xLzEwIiwiaWF0IjoxNTE0MjU0Nzc0LCJleHAiOjE1MTQyNTgzOTQsIm5iZiI6MTUxNDI1NDc5NCwianRpIjoiQ1VONFkyV3RyTEhVOEs4UCJ9.R-XMn2F88bpovEn1AReRxI5vk42UX1N8nAmyHBziAg4";
        $part = explode(".", $token);
        $header = $part[0];
        $payload = $part[1];
        $signature = $part[2];
        $_payload = base64_decode($payload);
        $_payload = json_decode($_payload, true); //返回数组
        return $payload;
    }

    /**
     * 登陆 返回 jwt_token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try{
            //fixme 未做$requestCheck
            $phone = $request->phone;
            $pass = $request->password;
            $user = User::where('phone', $phone)->first();
            if($user && Hash::check($pass, $user->password)) {
                if($user['status'] == 'offline'){
                    throw new OfflineException();
                }
                $jwt_token = JWTAuth::fromUser($user, ['scope' => $user['scope']]);//不使用JWTAuth::attemp,  为了记录登陆信息
                //todo 记录登陆时间
                $ip = $_SERVER['REMOTE_ADDR'];
                User::findOrFail($user['id'])->loginLogs()->create(["ip"=> $ip]);
                return $this->res(1000, $user->name . '已登陆成功', ['token' => $jwt_token]);

                //todo 短信服务
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
        }catch (BaseException $e){
            return $this->error($e);
        }


    }

    public function check()
    {
        //todo 如果通过了中间件, 自然返回ture
        return $this->res(1000, '登陆成功');
    }

    /**
     * 控制在2读以内
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findUser(Request $request)
    {
        $openid = $this->getOpenid($request->code);
        $emp = Employee::where('openid', $openid)->first();
        if($emp){
            $token = JWTAuth::fromUser($emp, ['com'=>$emp['company_id']]);
            return $this->res(6000, '已经通过, 同意跳转', $token);
        }else {
            if($data = Employee_waiting::with('errnos')->where('openid', $openid)->first()){
                if($data['status'] == '未通过'){
                    return $this->res(6002, '等待审核', $data);
                }else{
                    return $this->res(6003, '拒绝', $data);
                }
            }
            return $this->res(6001, '请注册');
        }
    }

    public function getJWT()
    {
//        $token = JWTAuth::fromUser(Employee::find(1));
//        return $token;
//        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9jdXMuYXBwL2FwaS92MS9zZXJ2aWNlcy9wYWdlLzEvMTUiLCJpYXQiOjE1MTQxODgyMDEsImV4cCI6MTUxNDE5MjA3NiwibmJmIjoxNTE0MTg4NDc2LCJqdGkiOiJlR0tJZ0N5ZVNEQ1ZNbjJhIn0.4BDSiUhXMViB7Ky7n341FeDRIIApPIGu_WUnpovesjo";
        $token ="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9jdXMuYXBwL2FwaS92MS90ZXN0IiwiaWF0IjoxNTEyOTc0NTEwLCJleHAiOjE1MTI5NzgxMTAsIm5iZiI6MTUxMjk3NDUxMCwianRpIjoiMmE4QlNPNVZmTUNjQnBqMSJ9.HBXKJ-Hrn4rspySH7FaWwPvlkfOPq_ulpyaJpC8ZXmk";
        $token = explode(".", $token);
        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
        return $payload;
    }


}
