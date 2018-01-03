<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\BaseException;
use App\Exceptions\LoginExp\RegMailException;
use App\Http\Repositories\MailRepository;
use App\Models\Check\Check_phone;
use App\Models\Employee_waiting;
use App\Services\Sms;
use Chenzeshu\ChenUtils\Traits\CurlFuncs;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmpWaitingController extends ApiController
{
    use CurlFuncs;

    protected $mail;

    function __construct(MailRepository $mail)
    {
        $this->mail = $mail;
    }

    public function page($page, $pageSize)
    {
        $begin =( $page - 1 ) * $pageSize;
        $emp = Employee_waiting::offset($begin)->limit($pageSize)->get();
        $count = Employee_waiting::count();
        $data = [
            'data' => $emp,
            'count' => $count
        ];

        return $this->res(200, '申请注册信息', $data);
    }

    public function store(Request $request)
    {
        try{
            //todo 通过openid判断是否重复提交申请
            $openid = $this->getOpenid($request->jscode);

            $request['openid'] = $openid;

            $check = $this->checkCode($request->phone, $request->phoneCode);
            if($check) {
                $data = Employee_waiting::create($request->except(['jscode', 'phoneCode']));
                $data['status'] = '未通过';
                return $this->res(2003, '申请成功, 请等待回复!', $data);
            }else{
                throw new RegMailException();
            }
        }catch (QueryException $e){
            return $this->res(-2003, '您已经提交过申请, 请耐心等待');
        }catch (RegMailException $e){
            return $this->error($e);
        }

    }

    /**
     * 管理员在后台修改信息, 如拒绝, 或修改并通过
     */
    public function update(Request $request)
    {
        
    }

    public function delete($id)
    {
        Employee_waiting::destroy($id);

        return $this->res(2005, '删除成功!');
    }

    /**
     * 存储验证码并以短信发送给用户
     * @param $phoneNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMsg($phoneNumber)
    {
        $code = $this->makeCode();
        Check_phone::create([
            'phone' => $phoneNumber,
            'code' => $code,
            'expire_at' => date('Y-m-d H:i:s', time()+300)
        ]);
        $re = $this->mail->sendCode($phoneNumber, $code);
        if($re){//$re true / false
            return $this->res(2003, "发送成功");
        }else{
            return $this->res(-2003, "发送失败");
        }
    }

    //查询进度
    public function search($openid)
    {

    }

    /**
     * 注册校验手机与验证码
     * @param string $phone
     * @param string $code
     * @return boolean $check
     */
    private function checkCode($phone, $code)
    {
        $data = DB::select("select id, code, created_at, expire_at from check_phones where status = 0 and phone = $phone");
        $check = false;
        foreach ($data as $d){
            if($d->code == $code && time() < strtotime($d->expire_at) ){
                $check = true;
                break;
            }
        }
        //验证成功, 才将所有code都过期, 否则如果其输错了验证码, 就会导致前面全部作废
        if($check == true && count($data) > 1){
            DB::update("update check_phones set status = 1 where status = 0 and phone = $phone");
        }
        return $check;
    }

    /**
     * 生成不重复的
     * @return array
     */
    private function makeCode(){
        $code = array();

        while(count($code) < 6){
            //产生随机数1-9
            $code[] = rand(1,9);
            //去除数组中的重复元素
            $code = array_unique($code);
        }

        return implode("", $code);
    }
}
