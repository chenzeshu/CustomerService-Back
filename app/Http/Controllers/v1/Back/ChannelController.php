<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\Channels\OutOfTimeException;
use App\Http\Helpers\Params;
use App\Http\Repositories\ChannelRepo;
use App\Http\Requests\channel\ChannelStoreRequest;
use App\Id_record;
use App\Models\Channels\Channel;
use App\Models\Channels\Contractc_plan;
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
        try{
            //todo 检查套餐余量
            $this->repo->checkPlan($request);
            //todo 信道服务单号
            $record = Id_record::find(5)->record;
            $len = 3 - strlen($record);
            $request['channel_id'] = date('Y', time()).zerofill($len).$record;

            //todo 通过校验后, 正式创建
            $channelModel = Channel::create($request->except(['id2','id3', 't1', 't2', 'id1','customer']));
            $channelModel->channel_applys()->create($request->only(['id1', 'id2','id3', 't1', 't2',]));
            return $this->res(2002, "新建信道服务单成功", ['data'=>$channelModel]);

        }catch (OutOfTimeException $e){
            return $this->error($e);
        }
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
        $re = Channel::find($id)->update($request->except(['customer','source_info', 'contractc', 'channel_applys']));

        //todo 假如将已完成改为拒绝, 则如何处置?
        //todo 假如将拒绝改为已完成, 则如何处置? 目前不更改记录


        if($re){
            //todo 失效缓存
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
        //todo 单纯地删除信道数据, 其他的将成为冗余
        //todo 尤其是已完成服务单的删除, 删除服务单不会造成损失
        //todo  或者说, 是以修代改, 最多是改成"拒绝"

        $re = Channel::find($id)->delete();
        if($re){
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }


}
