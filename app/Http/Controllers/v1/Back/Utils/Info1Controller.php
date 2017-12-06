<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Jobs\Cache\Utils;
use App\Models\Channels\Channel_info1;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Schedule;
class Info1Controller extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Channel_info1::offset($begin)->limit($pageSize)
            ->get()->toArray();
        $total = Channel_info1::count();
        $data = [
            'data' => $cons,
            'total' => $total
        ];
        return $this->res(200, '带宽类型', $data);
    }

    public function store(Request $request)
    {
        $data = Channel_info1::create($request->all());
        Utils::dispatch("info1");
        return $this->res(2002, "新建带宽成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $re = Channel_info1::find($id)->update($request->all());
        if($re){
            Utils::dispatch("info1");
            return $this->res(2003, "修改带宽成功");
        } else {
            return $this->res(-2003, "修改带宽失败");
        }
    }

    public function destroy($id)
    {
        $re = Channel_info1::destroy($id);
        if($re){
            Utils::dispatch("info1");
            return $this->res(2004, "删除带宽成功");
        } else {
            return $this->res(500, "删除带宽失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Channel_info1::where('name', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Channel_info1::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
