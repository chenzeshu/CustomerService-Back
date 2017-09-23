<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Requests\channel\ChannelStoreRequest;
use App\Models\Channels\Channel;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
    use ReturnTrait;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChannelStoreRequest $request)
    {
        $data = Channel::create($request->all());

        //fixme
        //触发新建申请单记录event + 填充关联单位 + 设备

        return $this->res(200, "新建信道服务单成功", ['data'=>$data]);
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
        //fixme 不支持修改合同单号, 所以前端只有灰色, 没有修改可能
        $re = Channel::find($id)->update($request->all());
        if($re){
            return $this->res(200, "修改服务单成功");
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
            return $this->res(200, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }
}
