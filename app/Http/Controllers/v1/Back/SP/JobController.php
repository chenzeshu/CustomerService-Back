<?php

namespace App\Http\Controllers\v1\Back\SP;


use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Repositories\JobRepo;
use App\Http\Resources\SP\serviceShowResource;
use App\Models\Employee;
use App\Models\Services\Contract_plan;
use App\Models\Services\Service;
use App\Models\Utils\Service_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

//派单
class JobController extends ApiController
{
    protected $repo;

    function __construct(JobRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 列出与自己有关的服务单
     */
    public function showServiceList($page, $pageSize, $emp_id, $status = "全部")
    {
        $data = ServiceDAO::getService($page, $pageSize, $emp_id, $status);
        $type = ServiceDAO::getServiceStatus();
        if(empty($data)){
            return $this->res(7000, '本状态下无服务单', ['data' => [], 'status' => $type]);
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
        $contract_id = $data['contract']['id'];  //todo 用于检索套餐使用详情, 因为下方提前使用了fractalAPI 不想改了, 赘生上去

        $data->customer = Employee::findOrFail($data->customer);
        if($data->man){
            $data->man = collect(explode(",", $data->man))->map(function($m){
                return Employee::findOrFail($m);
            });
        }

        $res = new serviceShowResource($data);
        $plan_id = $res['type']; //todo 用于检索套餐使用详情
        Log::info([
            'contract_id' => $contract_id,
            'plan_id' => $plan_id
        ]);
        //fixme 看来必须为service增加一个plan_id并且在选择时的前端也打通这个问题
        $use = Contract_plan::where('contract_id', 1)->where('plan_id', 9)->first();  //todo 检索套餐使用详情
        return [
            'data' => $res,
            'use' => $use
        ];
    }

    /**
     * 提前拿到服务单的一部分信息, 以防止无关人员通过检索服务单号查看详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceInfo(Request $request)
    {
        if( $emp_id     = $request->emp_id ){
            $service_id = $request->service_id;

            $service = Service::with(['contract', 'visits'])
                ->where('service_id', $service_id)
                ->first()
                ->toArray();

            if($this->repo->filterRelation($service, $emp_id)){
                return $this->res(7003, '已拉取', $service);
            }
            return $this->res(-7003, '不存在');  //此人无权限
        }else{
            return $this->res(-7003, '没有提供搜索者id');
        }
    }


    /**
     *  员工申请完成服务单
     */
    public function askFinish($service_id)
    {
        $re = Service::findOrFail($service_id)->update(['status'=>'申请完成']);
        Log::info("\r\n服务单请求申请:".date('Y-m-d H:i:s', time()));
        //todo 向管理员发送通知 + 后台展示

        if($re) return $this->res(7002, '申请成功');
    }
}
