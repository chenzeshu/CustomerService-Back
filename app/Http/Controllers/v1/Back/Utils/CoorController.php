<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Utils\Coor;
use Illuminate\Http\Request;

class CoorController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Coor::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Coor::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '普合信息', $data);
    }

    public function store(Request $request)
    {
        //contract_id规则写在前端

        //如果从company入口进入, 前端记录并并入了company_id
        $data = Coor::create($request->all());

        return $this->res(2002, "新建合同成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Coor::find($id)->update($request->all());
        if($re){
            return $this->res(2003, "修改合同成功");
        } else {
            return $this->res(-2003, "修改合同失败");
        }
    }

    public function destroy($id)
    {
        $re = Coor::destroy($id);
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
        $emp = Coor::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Coor::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
