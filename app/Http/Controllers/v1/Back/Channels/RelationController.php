<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Channels\Channel;
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
        //todo 检查是否重复
        $check = Channel_relation::where($request->all())->first();
        if($check){
            return $this->res(2006,'节点已存在');
        }
        $re = Channel_relation::create($request->all());
        if($re){
            //todo 刷新cache
            Channel::forget_cache();
            return $this->res(2003,'增加节点成功');
        }
    }

    public function deleteRelation(Request $request)
    {
        $re = Channel_relation::where('channel_apply_id', $request->channel_apply_id)
            ->where('device_id', $request->device_id)
            ->delete();
        //$re为删除的数目, 但是此处即使不为0 , 也不能进入if
//        if($re){
            //todo 刷新cache
            Channel::forget_cache();
            return $this->res(2005,'删除节点成功');
//        }
    }
}
