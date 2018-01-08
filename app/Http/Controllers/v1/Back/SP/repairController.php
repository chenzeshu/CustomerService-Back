<?php

namespace App\Http\Controllers\v1\back\SP;

use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Resources\SP\ServiceProcessCollection;
use App\Id_record;
use App\Models\Services\Service;
use Illuminate\Http\Request;

class repairController extends ApiController
{
    /**
     * 在选择的合同下创建服务单
     * @param $contract_id
     * @param Request $request
     */
    public function apply(Request $request)
    {
        //合同过期过滤已在 CommonController制作
        if($request->has('zhongId')){
            //中网员工报修
            $service_id = $this->generateId();
            $re = ServiceDAO::empCreate($service_id, $request);
        }else{
            //客户报修
            $service_id = $this->generateId();
            $re = ServiceDAO::cusCreate($service_id, $request);
        }
        if($re){
            //todo 向管理员发送一条短信
        }
        return $this->res(7004, '报修成功');
    }

    public function getProcess($page, $pageSize, $emp_id, $status)
    {
        $begin = ($page - 1) * $pageSize;
        $data = Service::with(['type','customer', 'refer_man', 'contract.company'])
            ->where('status', $status)
            ->where('refer_man', $emp_id)
            ->offset($begin)
            ->limit($pageSize)
            ->get();
        if( $data->count() == 0){
            return $this->res(-7003, '暂无数据');
        }else{
            return $this->res(7003, '报修进展列表', new ServiceProcessCollection($data));
        }

    }

}
