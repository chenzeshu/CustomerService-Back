<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\BaseException;
use App\Models\Check\Check_phone;
use App\Models\Employee_wating;
use App\Services\Sms;
use Chenzeshu\ChenUtils\Traits\CurlFuncs;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmpWaitingController extends ApiController
{
    use CurlFuncs;

    public function page($page, $pageSize)
    {
        $begin =( $page - 1 ) * $pageSize;
        $emp = Employee_wating::offset($begin)->limit($pageSize)->get();
        $count = Employee_wating::count();
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

            Employee_wating::create($request->except(['jscode']));
            return $this->res(2003, '申请成功, 请等待回复!');
        }catch (QueryException $e){
            return $this->res(-2003, '您已经提交过申请, 请耐心等待');
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
        Employee_wating::destroy($id);

        return $this->res(2005, '删除成功!');
    }

    public function sendMsg($phoneNumber)
    {
        $code = $this->makeCode();
        Check_phone::create([
            'phoneNumber' => $phoneNumber,
            'code' => $code
        ]);

//        (new Sms())->sendSms($signName, $templateCode, $phoneNumbers, $templateParam = null, $outId = null)

        return $this->res(2003, "发送成功");
    }

    public function checkCode($code)
    {
        $data = DB::select("select phone, code, created_at, expire");

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
