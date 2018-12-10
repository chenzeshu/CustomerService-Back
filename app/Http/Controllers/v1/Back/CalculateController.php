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
use Illuminate\Support\Facades\DB;

//todo 统计
class CalculateController extends ApiController
{
    public function basic()
    {
        //todo 未结清普通合同
        $contract_count = DB::table('service_moneys')->select(DB::raw('count(*) as count, finish'))->groupBy('finish')->get();
        //todo 未结清信道合同
        $contractc_count = DB::table('channel_moneys')->select(DB::raw('count(*) as count, finish'))->groupBy('finish')->get();
        //todo 待审核服务单
        $service_count =  DB::table('services')->select(DB::raw('count(*) as count, status'))->groupBy('status')->get();
        //todo 信道服务单
        $channel_count =  DB::table('channels')->select(DB::raw('count(*) as count, status'))->groupBy('status')->get();
        //todo 公司
        $company_count = DB::table('companies')->select(DB::raw('count(*) as count, type'))->groupBy('type')->get();
        //todo 故障状态
        $problem_count = DB::table('problems')->select(DB::raw('count(*) as count, problem_step'))->groupBy('problem_step')->get();



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
               "problem_count" => $problem_count,
           ],
            "loginInfo" => $loginInfo
        ]);
    }

}
