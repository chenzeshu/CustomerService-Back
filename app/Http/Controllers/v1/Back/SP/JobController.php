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

//派单
class JobController extends ApiController
{
    /**
     * 列出与自己有关的服务单
     */
    public function showServiceList($page, $pageSize, $emp_id, $status = "已派单")
    {
        $data = ServiceDAO::getService($page, $pageSize, $emp_id, $status);
        if(empty($data)){
            return $this->res(7000, '暂无服务');
        }
        $type = $this->getServiceStatus();
        $data = [
            'data' => $data,
            'status' => $type
        ];

        return $this->res(7001, '服务信息', $data);
    }

    /**
     * 得到服务单状态(二维数组{id:xx, status:xx})
     * @return \Illuminate\Config\Repository|mixed
     */
    private function getServiceStatus(){
        $status = Cache::get('service_status');
        if(empty($status)){
            $status = config('app.status');
            $type = [];
            foreach ($status as $k=>$s){
                $type[] = ['id' =>++$k, 'status'=>$s];
            }
            Cache::put('service_status', $type, 86400);
        }
        return $status;
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


}
