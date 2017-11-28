<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Channels\Channel_relation;
use Illuminate\Http\Request;

//todo 节点控制器
class RelationController extends ApiController
{
    /**
     * 增加信道服务单的关联节点
     */
    public function addDeviceToChannel(Request $request)
    {
        $re = Channel_relation::create($request->all());
        if($re){
            return $this->res(2003,'增加节点成功');
        }
    }

    public function deleteRelation(Request $request)
    {
        $re = Channel_relation::where('channel_apply_id', $request->channel_apply_id)
            ->where('device_id', $request->device_id)
            ->delete();
        if($re){
            return $this->res(2005,'删除节点成功');
        }
    }
}