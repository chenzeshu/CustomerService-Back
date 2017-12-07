<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Repositories\ChannelRepo;
use App\Http\Requests\channel\ChannelStoreRequest;
use App\Models\Channels\Channel;
use Illuminate\Support\Facades\Cache;

class ChannelController extends ApiController
{
    protected $repo ;
    function __construct(ChannelRepo $repo)
    {
        $this->repo = $repo;
    }
    /**
     * @param $page
     * $other 为配合前端预留的参数, 目前channelController用不上
     */
    //todo 缓存分页
    public function page($page, $pageSize, $status = "", $other = "")
    {
        $channels = Cache::get('channels');
        if(empty($channels)){
            Channel::redis_refresh_data();
            $channels = Cache::get('channels');
        }
        list($channels, $total) = $this->repo->pageFilter($channels, $status, $page, $pageSize);
        $cache = Channel::get_cache();
        $data = [
            'data' => $channels,
            'total' => $total,
        ];
        $data = array_merge($data, $cache);
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

        //todo 失效缓存
        Channel::forget_cache();
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
            //todo 失效缓存
            Channel::forget_cache();
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
            //todo 失效缓存
            Channel::forget_cache();
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }
}
