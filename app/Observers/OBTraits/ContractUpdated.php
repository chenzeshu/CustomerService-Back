<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17
 * Time: 17:05
 */

namespace App\Observers\OBTraits;

use App\Models\Contract;

//涉及到普通合同的模型变动后对普通合同缓存派系的更新
Trait ContractUpdated
{
    public static function bootContractUpdated()
    {
        foreach (static::getModelEvents() as $event){
            static::$event(function ($model){
                $model->setContract();
            });
        }
    }
    public static function getModelEvents()
    {
        if(isset(static::$recordEvents)){
            return static::$recordEvents;
        }
        return ['updated'];  //默认是更新
    }

    //todo 更新合同模型缓存
    public function setContract()
    {
        Contract::forget_cache();
    }
}