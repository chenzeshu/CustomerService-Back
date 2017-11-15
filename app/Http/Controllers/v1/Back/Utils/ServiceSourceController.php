<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Utils\Service_source;
use Illuminate\Http\Request;

class ServiceSourceController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Service_source::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Service_source::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '普合信息', $data);
    }

    public function store(Request $request)
    {
        //如果从company入口进入, 前端记录并并入了company_id
        $data = Service_source::create($request->all());

        return $this->res(2002, "新建合同成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Service_source::find($id)->update($request->all());
        if($re){
            return $this->res(2003, "修改合同成功");
        } else {
            return $this->res(-2003, "修改合同失败");
        }
    }

    public function destroy($id)
    {
        $re = Service_source::destroy($id);
        if($re){
            return $this->res(2004, "删除合同成功");
        } else {
            return $this->res(500, "删除合同失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Service_source::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Service_source::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
