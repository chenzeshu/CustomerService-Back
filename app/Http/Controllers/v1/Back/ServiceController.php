<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Requests\Service\ServiceStoreRequest;
use App\Models\Services\Service;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    use ReturnTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->res(200, 'Service');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        //创建时前端直接不提供`已派单`选项
        //todo 然后后台也做一次检查
        $this->checkStatus($request);

        //todo 存储
        $data = Service::create($request->all());
        return $this->res(200, "新建信道服务单成功", ['data'=>$data]);
    }

    //todo 派单时的方法及触发短信/邮件/内部通知
    public function waitingWork()
    {
        
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
    public function update(ServiceStoreRequest $request, $id)
    {
        //fixme 不支持修改合同单号, 所以前端只有灰色, 没有修改可能
        //fixme 当状态修改为派单时, 不支持修改, 修改派单的API在上面
        $this->checkStatus($request);

        //todo 修改
        $re = Service::find($id)->update($request->all());
        if($re){
            return $this->res(200, "修改服务单成功");
        } else {
            return $this->res(500, "修改服务单失败");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $re = Service::find($id)->delete();
        if($re){
            return $this->res(200, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }



    private function checkStatus($request){
        if($request->has('status') && $request->status == '已派单'){
            return $this->res(500, "请点击右侧派单功能");
        }
    }
}
