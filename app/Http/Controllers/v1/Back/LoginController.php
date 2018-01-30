<?php

namespace App\Http\Controllers\v1\Back;

use App\Dao\ServiceDAO;
use App\Exceptions\BaseException;
use App\Exceptions\LoginExp\OfflineException;
use App\Exceptions\LoginExp\WrongInputExp;
use App\Exceptions\SP\ChannelNoCheckerExp;
use App\Http\Resources\Back\ServiceVerifyCollection;
use App\Http\Resources\SP\serviceCompanyCollection;
use App\Http\Resources\SP\serviceCompanyResource;
use App\Http\Resources\SP\ServiceProcessCollection;
use App\Http\Resources\SP\serviceShowResource;
use App\Models\Channels\Channel;
use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_plan;
use App\Models\Company;
use App\Models\Contractc;
use App\Models\Employee;
use App\Models\Employee_waiting;
use App\Models\Services\Contract_plan;
use App\Models\Services\Service;
use App\Models\Utils\Service_type;
use App\Observers\OBTraits\ContractcUpdated;
use App\Services\Sms;
use App\User;
use Chenzeshu\ChenUtils\Traits\CurlFuncs;
use Chenzeshu\ChenUtils\Traits\TestTrait;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function test(Request $request)
    {
        $content = "诺依曼 区";
        $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        $client = ClientBuilder::create()->build();

        $json = '{
                  "query":{
                    "multi_match":{
                      "query":'.$content.',
                      "fields": ["name", "address"]
                    }
                  }
                }';


        $params = [
            'index' => 'cs',
            'type' => 'company',
            'body' => $json
        ];
        $data = $client->search($params);

        $data= [
            'data'=> $data['hits']['hits'],
            'total'=> count($data['hits']['hits']),
        ];
        return $this->res(200, '搜索结果', $data);

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
     *
     * 通过code求得openid, 控制在2读以内
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findUser(Request $request)
    {
        $openid = $this->getOpenid($request->code);
        return $this->findUserViaOpenid($openid);
    }


    public function findUserViaOpenid($openid)
    {
        $emp = Employee::where('openid', $openid)->first();
        if($emp){
            $token = JWTAuth::fromUser($emp, ['com'=>$emp['company_id']]);
            return $this->res(6000, '已经通过, 同意跳转', [
                'token' => $token,
                'openid' => $openid
            ]);
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
