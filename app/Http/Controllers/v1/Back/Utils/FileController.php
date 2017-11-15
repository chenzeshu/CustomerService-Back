<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Doc;
use Illuminate\Http\Request;

class FileController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Doc::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Doc::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '普合信息', $data);
    }

//    public function store(Request $request)
//    {
//        $data = Doc::create($request->all());
//
//        return $this->res(2002, "新建合同成功", ['data'=>$data]);
//    }

    public function update(Request $request, $id)
    {
        $re = Doc::find($id)->update($request->all());
        if($re){
            return $this->res(2003, "修改合同成功");
        } else {
            return $this->res(-2003, "修改合同失败");
        }
    }

    public function destroy($id)
    {
        $re = Doc::destroy($id);
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
        $emp = Doc::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Doc::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
