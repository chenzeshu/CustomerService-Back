<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Jobs\Cache\Utils;
use App\Models\Channels\Channel_info3;
use Illuminate\Http\Request;

class Info3Controller extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Channel_info3::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Channel_info3::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '普合信息', $data);
    }

    public function store(Request $request)
    {
        $data = Channel_info3::create($request->all());
        Utils::dispatch("info3");
        return $this->res(2002, "新建成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $re = Channel_info3::find($id)->update($request->all());
        if($re){
            Utils::dispatch("info3");
            return $this->res(2003, "修改成功");
        } else {
            return $this->res(-2003, "修改失败");
        }
    }

    public function destroy($id)
    {
        $re = Channel_info3::destroy($id);
        if($re){
            Utils::dispatch("info3");
            return $this->res(2004, "删除成功");
        } else {
            return $this->res(500, "删除失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Channel_info3::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Channel_info3::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
