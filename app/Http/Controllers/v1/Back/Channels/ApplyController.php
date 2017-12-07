<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Http\Controllers\v1\Back\ApiController;
use App\Jobs\Cache\RefreshChannels;
use App\Models\Channels\Channel;
use App\Models\Channels\Channel_apply;
use App\Models\Channels\Channel_operative;
use App\Models\Channels\Channel_real;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

//信道申请表
class ApplyController extends ApiController
{
    public function page($page, $pageSize)
    {
        $begin = ($page - 1) * $pageSize;
        $applies = Channel_apply::get_pagination($begin, $pageSize);
        $total = Channel_apply::get_total();
        list($tongxin, $jihua, $daikuan) = Channel_apply::get_cache();
        $data = [
            'data' => $applies,
            'total' => $total,
            'tongxin'=>$tongxin,
            'jihua' => $jihua,
            'daikuan'=>$daikuan
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
        $model = Channel_apply::findOrFail($id);
        $re1 = $model->update($request->except('channel_relations'));
        $re2 = $model->channel()->update([
            'status'=>'运营调配'
        ]);
        if($re1 && $re2){
            RefreshChannels::dispatch();
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
     * 更新运营表, 若不存在则创建
     * $id apply_id
     */
    public function updateOperative(Request $request, $id)
    {
        $model = Channel_operative::where('channel_apply_id', $request->channel_apply_id);
        if($model->first()){
            $re = $model->update($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
        }else {
            $re = Channel_apply::findOrFail($id)->channel_operative()->create($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
        }
        if($re){
            return $this->res(2003, '审核通过');
        }
    }

    /**
     * $id apply_id
     * 更新实际运行表, 若不存在则创建
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
