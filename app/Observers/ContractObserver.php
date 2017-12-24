<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17
 * Time: 16:01
 */

namespace App\Observers;


use App\Http\Controllers\v1\Back\ServiceController;
use App\Id_record;
use App\Models\Contract;

class ContractObserver
{
    /**
     * 监听创建/更新
     */
    public function saved()
    {
        Contract::forget_cache();
    }

    /**
     * 创建合同的同时建立一个新的回款记录总单
     * @param Contract $contract
     */
    public function created(Contract $contract)
    {
        $contract->ServiceMoney()->create([]);
        if($contract->type2 == "销售"){
            Id_record::find(1)->increment('record');
        }else{
            Id_record::find(2)->increment('record');
        }
    }

    public function deleting(Contract $contract)
    {
        //执行服务单删除逻辑
        $services = $contract->services()->get();
        $controller = new ServiceController();
        foreach ($services as $service){
            $controller->destroy($service->id);
        }
    }
    
    public function deleted()
    {
        Contract::forget_cache();
    }
}