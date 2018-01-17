<?php

namespace App\Http\Controllers\v1\Back\SP;

use App\Dao\ChannelDAO;
use App\Http\Resources\SP\Channel\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
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
    public function searchContract($company_id)
    {
        $nowaTime = date('y-m-d', time());
        $data = Company::findOrFail($company_id)->contracts()->get()->reject(function($contract) use ($nowaTime){
            //过滤已过期合同
            if($contract->deadline < $nowaTime){
                return true;
            }
        })->toArray();

        if(empty($data)){  //empty必须要数组 [],  collect也不行
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '合同列表', $data);
    }
}
