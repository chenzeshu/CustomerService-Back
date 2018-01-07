<?php

namespace App\Http\Controllers\v1\Back\SP;


use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Resources\SP\serviceShowResource;
use App\Models\Employee;
use App\Models\Services\Service;
use App\Models\Utils\Service_type;
use Illuminate\Http\Request;

class JobController extends ApiController
{
    /**
     * 列出与自己有关的服务单
     */
    public function showServiceList($page, $pageSize, $emp_id)
    {
        $data = ServiceDAO::getService($page, $pageSize, $emp_id);
        if(empty($data)){
            return $this->res(7000, '暂无服务');
        }
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
        $data->type = Service_type::findOrFail($data->type)->name;

        return new serviceShowResource($data);
    }


}
