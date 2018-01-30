<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/24
 * Time: 15:00
 */

namespace App\Observers;

use App\Id_record;
use App\Models\Contractc;

class ContractcObserver
{
    /**
     * 监听创建/更新
     */
    public function saved()
    {
        Contractc::forget_cache();
    }

    /**
     * 创建合同的同时建立一个新的回款记录总单
     * @param Contract $contract
     */
    public function created(Contractc $contractc)
    {
        $contractc->ChannelMoney()->create([]);
        Id_record::find(3)->increment('record');
    }

    public function deleting(Contractc $contractc)
    {

    }

    public function deleted()
    {
        Contractc::forget_cache();
    }
}