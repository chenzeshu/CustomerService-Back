<?php

namespace App\Http\Controllers\v1\Back\Contracts;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Services\Contract_planutil;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
       $data = Contract_planutil::create($request->all());
       if($data){
           return $this->res(2002, '创建成功');
       }else{
           return $this->res(-2002, '创建失败');
       }
    }

    public function update($id, Request $request)
    {
        $re = Contract_planutil::findOrFail($id)->update($request->all());
        if($re){
            return $this->res(2003, '更新成功');
        }else{
            return $this->res(-2003, '更新失败');
        }
    }

    public function destroy($id)
    {
        $re = Contract_planutil::destroy($id);
        if($re){
            return $this->res(2004, '删除成功');
        }else{
            return $this->res(-2004, '删除失败');
        }
    }

}
