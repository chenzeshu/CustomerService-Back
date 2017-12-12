<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Channels\Channel_duty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DutyController extends ApiController
{
    public function page($page, $pageSize)
    {
        $data  = Channel_duty::getData($page, $pageSize);
        return $this->res(200, '值班数据', $data);
    }

    public function store(Request $request)
    {
        //如果从company入口进入, 前端记录并并入了company_id
        $data = Channel_duty::create($request->all());
        return $this->res(2002, "新建值班数据", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Channel_duty::find($id)->update($request->all());
        if($re){
            return $this->res(2003, "修改值班数据成功");
        } else {
            return $this->res(-2003, "修改值班数据失败");
        }
    }

    public function destroy($id)
    {
        $re = Channel_duty::destroy($id);
        if($re){
            return $this->res(2004, "删除值班数据成功");
        } else {
            return $this->res(500, "删除值班数据失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $model = DB::table('employees')->where('name', 'like', "%".$name."%")
            ->leftJoin('channel_duties', 'employees.id', "=", "channel_duties.employee_id")
            ->select('channel_duties.id','channel_duties.employee_id' ,'channel_duties.t1','channel_duties.t2','channel_duties.remark')
            ->orderBy('channel_duties.id', 'desc')
            ->get()
            ->reject(function ($v){
                return $v->id == null;
            });

        $emp = $model->each(function ($item){
                    $item->checker = $item->employee_id == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->employee_id})");
                })
                ->toArray();

        $total = $model->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
