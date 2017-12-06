<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Jobs\Cache\Utils;
use App\Models\Channels\Channel_info1;
use App\Models\Channels\Channel_info6;
use App\Models\Utils\Plan;
use Illuminate\Http\Request;

class PlanController extends ApiController
{
    /**
     * @desc $info1 网络类型
     * @desc $info6 带宽类型
     */
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Plan::offset($begin)->limit($pageSize)
            ->get()
            ->toArray();
        $info1s = Channel_info1::all()->toArray();
        $info6s = Channel_info6::all()->toArray();
        $total = Plan::count();
        $data = [
            'data' => $cons,
            'total' => $total,
            'info1s'=> $info1s,
            'info6s'=> $info6s,
        ];
        return $this->res(200, '普合信息', $data);
    }

    public function store(Request $request)
    {
        //如果从company入口进入, 前端记录并并入了company_id
        $data = Plan::create($request->all());
        Utils::dispatch("plans");
        return $this->res(2002, "新建合同成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Plan::find($id)->update($request->all());
        if($re){
            Utils::dispatch("plans");
            return $this->res(2003, "修改合同成功");
        } else {
            return $this->res(-2003, "修改合同失败");
        }
    }

    public function destroy($id)
    {
        $re = Plan::destroy($id);
        if($re){
            Utils::dispatch("plans");
            return $this->res(2004, "删除合同成功");
        } else {
            return $this->res(500, "删除合同失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Plan::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Plan::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
