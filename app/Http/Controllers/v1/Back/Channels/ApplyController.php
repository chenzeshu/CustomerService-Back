<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Channels\Channel;
use App\Models\Channels\Channel_apply;
use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_info4;
use App\Models\Channels\Channel_info5;
use App\Models\Channels\Channel_operative;
use App\Models\Channels\Channel_real;
use Illuminate\Http\Request;

class ApplyController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ($page - 1) * $pageSize;
        $applies = Channel::where('status','=','待审核')
            ->offset($begin)
            ->limit($pageSize)
            ->with(['employee.company' ,'contractc',
                'channel_applys.channel_relations.company',
                'channel_applys.channel_relations.device',
                'plans', 'tongxin','jihua', 'pinlv', 'source'])
            ->get()
            ->toArray();
        $total = Channel::where('status','=','待审核')->count();
        //fixme 工具表的检索, 建议无更新每周一次/有更新时更新, 存到缓存里去, 不然太占代码量
        $tongxin = Channel_info3::all()->toArray();
        $jihua = Channel_info5::all()->toArray();
        $pinlv = Channel_info4::all()->toArray();
        $data = [
            'data' => $applies,
            'total' => $total,
            'tongxin'=>$tongxin,
            'jihua'=>$jihua,
            'pinlv'=>$pinlv
        ];

        return $this->res(200, '待审核', $data);
    }

    /**
     * 补完申请并通过审核进入"运营调配"
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $re1 = Channel_apply::findOrFail($id)->update($request->except('channel_relations'));
        $re2 = Channel_apply::findOrFail($id)->channel()->update([
            'status'=>'运营调配'
        ]);
        if($re1 && $re2){
            return $this->res(2003, '审核通过');
        }
    }

    /**
     * 拒绝, 不通过审核
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rej($id)
    {
        $re = Channel::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);
        if($re){
            return $this->res(200, '已拒绝');
        }
    }

    /**
     * 更新运营表
     * $id apply_id
     */
    public function updateOperative(Request $request, $id)
    {
        $model = Channel_operative::where('channel_apply_id', $request->channel_apply_id);
        if($model->first()){
            $re = $model->update($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
        }else {
            $re = Channel_apply::findOrFail($id)->channel_real()->create($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
        }
        if($re){
            return $this->res(2003, '审核通过');
        }
    }

    /**
     * $id apply_id
     * 更新实际运行表
     */
    public function updateReal(Request $request, $id)
    {
        $model = Channel_real::where('channel_apply_id', $request->channel_apply_id);
        if($model->first()){
            $re = $model->update($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));

        }else {
            $re = Channel_apply::findOrFail($id)->channel_real()->create($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
        }

        if($re){
            return $this->res(2003, '审核通过');
        }
    }
}
