<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/13
 * Time: 18:25
 */

namespace App\Observers;


use App\Models\Contractc;
use Illuminate\Support\Facades\Log;

class ContractPlanOb
{
    public function saved()
    {
        Contractc::forget_cache();
        Log::info('再测试');
    }

    /**
     * 创建合同的同时建立一个新的回款记录总单
     * @param 
     */
    public function created()
    {
        Contractc::forget_cache();
    }

    public function deleted()
    {
        Contractc::forget_cache();
    }
}