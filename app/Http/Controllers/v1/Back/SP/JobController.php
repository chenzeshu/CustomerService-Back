<?php

namespace App\Http\Controllers\v1\Back\SP;


use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Resources\SP\serviceShowResource;
use App\Models\Employee;
use App\Models\Services\Service;
use App\Models\Utils\Service_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

//派单
class JobController extends ApiController
{
    /**
     * 列出与自己有关的服务单
     */
    public function showServiceList($page, $pageSize, $emp_id, $status = "已派单")
    {
        $data = ServiceDAO::getService($page, $pageSize, $emp_id, $status);
        $type = ServiceDAO::getServiceStatus();
        if(empty($data)){
            return $this->res(7000, '暂无服务', ['status' => $type]);
        }
        $data = [
            'data' => $data,
            'status' => $type
        ];
        return $this->res(7001, '服务信息', $data);
    }


    /**
     * 显示服务单详情
     */
    public function showServiceDetail($service_id)
    {
        $data = Service::with(['contract.company', 'type'])->findOrFail($service_id);
        $data->customer = Employee::findOrFail($data->customer);
        $data->pm = collect(explode(",", $data->contract['PM']))->map(function($pm){
            return Employee::findOrFail($pm);
        });
        return new serviceShowResource($data);
    }

    /**
     *  员工申请完成服务单
     */
    public function askFinish($service_id, Request $request)
    {
        Log::info("service_id is $service_id\r\n");
        Log::info("是否含有文件:".$request->hasFile('wxFiles')."\r\n");
        Log::info("POST:" .$request."\r\n");
        /*
        $re = Service::findOrFail($service_id)->update(['status'=>'申请完成']);
        if($re) return $this->res(7002, '申请成功');
        */
    }


}
