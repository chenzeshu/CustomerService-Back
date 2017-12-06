<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Requests\channel\ChannelStoreRequest;
use App\Models\Channels\Channel;
use App\Models\Channels\Channel_info2;
use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_info4;
use App\Models\Channels\Channel_info5;
use App\Models\Utils\Plan;
use App\Models\Utils\Service_source;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChannelController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->res(200, 'channels');
    }


    /**
     * @param $page
     * $other 为配合前端预留的参数, 目前channelController用不上
     */
    public function page($page, $pageSize, $status = "", $other = "")
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Channel::get_pagination($status, $begin, $pageSize);
        $total = Channel::get_total($status);
        list($service_sources, $tongxins, $jihuas, $plans, $zhantypes) = Channel::get_cache();

        $data = [
            'data' => $cons,
            'total' => $total,
            'sources' => $service_sources,
            'jihuas' => $jihuas,
            'tongxins' => $tongxins,
            'plans' => $plans,
            'zhantypes'=>$zhantypes,
        ];
        return $this->res(200, '信道服务单', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChannelStoreRequest $request)
    {
        $data = Channel::create($request->except(['customer','source_info']));

        //fixme
        //触发新建申请单记录event + 填充关联单位 + 设备

        return $this->res(2002, "新建信道服务单成功", ['data'=>$data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ChannelStoreRequest $request, $id)
    {
        //fixme 不支持修改信号服务单号, 所以前端只有灰色, 没有修改可能
        $re = Channel::find($id)->update($request->except(['customer','source_info', 'contractc', 'channel_applys']));
        if($re){
            return $this->res(2003, "修改服务单成功");
        } else {
            return $this->res(500, "修改服务单失败");
        }

        //others
        //todo 修改信道的细节在其他地方
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $re = Channel::find($id)->delete();
        if($re){
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }
}
