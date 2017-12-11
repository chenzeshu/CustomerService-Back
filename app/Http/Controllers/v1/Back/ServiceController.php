<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Requests\Service\ServiceStoreRequest;
use App\Http\Traits\UploadTrait;
use App\Models\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends ApiController
{
    use UploadTrait;

    function __construct()
    {
        $this->save_path = "services";
    }

    /**
     * 分页
     * @param $page
     * @param $pageSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize, $status="", $charge_flag="")
    {
        $begin = ( $page -1 ) * $pageSize;
        $services = Service::get_pagination($status, $charge_flag, $begin, $pageSize);
        $total = Service::get_total($status, $charge_flag);
        list($service_types, $service_sources) = Service::get_cache();
        $data = [
            'data' => $services,
            'total' => $total,
            'types' => $service_types,
            'sources' => $service_sources
        ];
        return $this->res(200, '服务单信息', $data);
    }

    /**
     * 筛选出待审核的服务单
     */
    public function verify()
    {
        $emp = Service::where('status','=','待审核')
            ->with(['customer', 'contract', 'source', 'type', 'refer_man.company'])
            ->get()
            ->each(function ($ser){
                $ser['project_manager'] = $ser['contract']['PM'] == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->contract->PM})");
            })
            ->toArray();
        $total = Service::where('status','=','待审核')->count();
        $data = [
            'data' => $emp,
            'total' => $total,
        ];
        return $this->res(200, '待审核服务申请', $data);
    }

    /**
     * 通过未审核服务单
     */
    public function pass($id)
    {
        $re = Service::findOrFail($id)->update([
            'status'=>'待派单'
        ]);
        return $this->res(200, '审核通过, 用户将收到通知');
    }

    public function rej($id)
    {
        $re = Service::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);
        return $this->res(200, '已拒绝, 用户将收到通知');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        //todo 前端右侧可以做一个派单

        //todo  文件上传
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }

        //todo 检查响应时间
        if($request->status == "待审核" || $request->status == "已派单"){
            $request['time3'] = date('Y-m-d H:i:s', time());
        }

        //todo 存储
        $data = Service::create($request->all());
        return $this->res(2002, "新建信道服务单成功", $data);
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
        $update = Service::find($id);
        //todo 文件
        if($request->has('fileList')){
            //todo 检查过滤新旧文件
            $doc_id =  Service::where('id', $id)->first(['document']);
            $request['document'] = $this->getFinalIds($request, $doc_id);
            unset($request['fileList']);
        }

        //fixme 不支持修改合同单号, 所以前端只有灰色, 没有修改可能
        if($request->status == "待审核" && $update->status != "待审核"){  //变成待审核后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }
        if($request->status == "已派单" && $update->status != "已派单"){   //变成已派单后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }
        //todo 修改
        $re = $update->update($request->except(['contract','company', 'visits']));
        if($re){
            return $this->res(2003, "修改服务单成功");
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
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }

    /**
     * 回访
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visit(Request $request, $id)
    {
        $re = Service::findOrFail($id)->visits()->updateOrCreate($request->except(['employees', 'status','id']));
        if($re){
            return $this->res(2005, "填写回访成功");
        } else {
            return $this->res(500, "填写回访失败");
        }
    }

}
