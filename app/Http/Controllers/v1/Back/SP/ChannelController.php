<?php

namespace App\Http\Controllers\v1\Back\SP;

use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Resources\SP\Channel\DeviceCollection;
use App\Models\Channels\Contractc_plan;
use App\Models\Company;
use App\Models\Utils\Device;
use Illuminate\Support\Facades\Cache;

class ChannelController extends ApiController
{
    /**
     * @param $page 当前页数
     * @param $pageSize 每页数量
     * @param $emp_id 人员id
     * @param $status 信道单状态
     */
    public function page($page, $pageSize, $emp_id, $status)
    {

    }

    /**
     * 检索信道合同
     */
    public function searchContractc($company_id)
    {
        $nowaTime = date('y-m-d', time());
        $data = Company::findOrFail($company_id)->contract_cs()->get()->reject(function($contract) use ($nowaTime){
            //过滤已过期合同
            if($contract->deadline < $nowaTime){
                return true;
            }
        })->toArray();

        $params  = Cache::many(['tongxins','jihuas']);  //通信卫星 + 计划

        if(empty($data)){  //empty必须要数组 [],  collect也不行
            return $this->res(7004, '查无结果', $params);
        }
        $data = [
          'data' => $data,
          'params' => $params
        ];
        return $this->res(7003, '合同列表', $data);
    }

    /**
     * 搜索套餐
     */
    public function searchPlan($contractc_id)
    {
        $data = Contractc_plan::where('contractc_id', $contractc_id)
            ->get()
            ->reject(function($item){
                //先过滤掉无量套餐
                return $item->total === $item->use;
            })
            ->toArray();
        if(empty($data)){  //empty必须要数组 [],  collect也不行
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '合同列表', $data);
    }

    public function searchDevice($company_id)
    {
        $data = Device::where('company_id', $company_id)->where('status', '!=', '停用')
            ->get();

        if($data->count() == 0){
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '设备列表', new DeviceCollection($data));
    }
}
