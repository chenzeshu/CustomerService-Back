<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Models\Channels\Channel;
use App\Models\Company;
use App\Models\Money\ChannelMoney;
use App\Models\Money\ServiceMoney;
use App\Models\Services\Service;
use App\User;
use Illuminate\Support\Facades\Cache;

//todo 统计
class CalculateController extends ApiController
{
    public function basic()
    {
        //todo 未结清普通合同
        $contract_count = ServiceMoney::where('finish', '未结清')->count();
        //todo 未结清信道合同
        $contractc_count = ChannelMoney::where('finish', '未结清')->count();
        //todo 待审核服务单
        $service_count =  Service::where('status', '待审核')->count();
        //todo 待审核信道服务单
        $channel_count =  Channel::where('status', '待审核')->count();
//        todo 所有公司数量
        $company_count = Company::count();


        //todo 拿到上次登陆时间
        //fixme 不用缓存锁的缺陷在于高并发时, 脏读
        $loginInfo = Cache::get('loginInfo');
        //fixme 高并发时, 还没到下一步, 另一个线程也同时读取了
        Cache::forget('loginInfo');

        return $this->res(200, '基本统计', [
           "calData" => [
               "contract_count" => $contract_count,
               "contractc_count" => $contractc_count,
               "service_count" => $service_count,
               "channel_count" => $channel_count,
               "company_count" => $company_count,
           ],
            "loginInfo" => $loginInfo
        ]);
    }

}
