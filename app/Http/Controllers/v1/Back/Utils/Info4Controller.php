<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Channels\Channel_info4;
use Illuminate\Http\Request;


class Info4Controller extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Channel_info4::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Channel_info4::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '普合信息', $data);
    }

    public function store(Request $request)
    {
        $data = Channel_info4::create($request->all());

        return $this->res(2002, "新建成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $re = Channel_info4::find($id)->update($request->all());
        if($re){
            return $this->res(2003, "修改成功");
        } else {
            return $this->res(-2003, "修改失败");
        }
    }

    public function destroy($id)
    {
        $re = Channel_info4::destroy($id);
        if($re){
            return $this->res(2004, "删除成功");
        } else {
            return $this->res(500, "删除失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Channel_info4::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Channel_info4::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
