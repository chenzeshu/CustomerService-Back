<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17
 * Time: 17:05
 */

namespace App\Observers\OBTraits;

use App\Models\Contractc;
use Illuminate\Support\Facades\Log;

//涉及到普通信道合同的模型变动后对普通信道合同缓存派系的更新
Trait ContractcUpdated
{
    public static function bootContractcUpdated()
    {
        foreach (static::getModelEvents() as $event){
            static::$event(function ($model){
                $model::setContractc();
            });
        }
    }
    public static function getModelEvents()
    {
        if(isset(static::$recordEvents)){
            return static::$recordEvents;
        }
        return ['updated','saved'];  //默认是更新
    }

    //todo 更新合同模型缓存
    public static function setContractc()
    {
        Contractc::forget_cache();
    }

}