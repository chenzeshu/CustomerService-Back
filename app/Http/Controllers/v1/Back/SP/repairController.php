<?php

namespace App\Http\Controllers\v1\back\SP;

use App\Http\Controllers\v1\Back\ApiController;
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
            $re = Service::create([
                'contract_id' => $request->contract_id,
                'service_id' => $service_id,
                'type' => $request->type_id,
                'customer'=> $request->cus_id,
                'refer_man' => $request->zhongId
            ]);
        }else{
            //客户报修
            $service_id = $this->generateId();
            $re = Service::create([
                'contract_id' => $request->contract_id,
                'service_id' => $service_id,
                'type' => $request->type_id,
                'customer'=> $request->cus_id,
                'refer_man' => $request->cus_id
            ]);
        }
        if($re){
            //todo 向管理员发送一条短信
        }
        return $this->res(7004, '报修成功');
    }

    /**
     * 生成service单号
     */
    private function generateId(){
        //todo 自动生成服务单编号
        $record = Id_record::find(4)->record;
        $len = 3 - strlen($record);
        return  date('Y', time()).zerofill($len).$record;
    }
}
