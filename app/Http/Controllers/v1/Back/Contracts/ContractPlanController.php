<?php

namespace App\Http\Controllers\v1\Back\Contracts;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Services\Contract_planutil;

//用于展示/维护服务合同的套餐, 不是外键表, 外键表在ContractController
class ContractPlanController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ($page - 1) * $pageSize;
        $plans = Contract_planutil::offset($begin)->limit($pageSize)->get()->toArray();
        $total = Contract_planutil::count();
        $data = [
          'data' => $plans,
          'total' => $total
        ];
        return $this->res(200, '套餐详情', $data);
    }


}
