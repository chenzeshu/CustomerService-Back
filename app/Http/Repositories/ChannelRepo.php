<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 8:48
 */

namespace App\Http\Repositories;


use App\Exceptions\Channels\OutOfTimeException;
use App\Http\Helpers\Params;
use App\Models\Channels\Contractc_plan;

class ChannelRepo
{
    public function pageFilter($channels, $status, $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $data = collect($channels)->reject(function($item) use ($status){
            if($status != ""){
                return $item['status'] != $status;
            }
        });
        $total = count($data);
        $data = $data->splice($begin, $pageSize);
        return [$data, $total];
    }

    //todo 校验套餐的余量, 但不做减少, 直到实际运行才减少
    public function checkPlan($request){
        $planModel = Contractc_plan::findOrFail($request->id1);
        $curTime = ceil((strtotime($request->t2) - strtotime($request->t1))/ Params::ChannelTime);
        $check = $planModel->total - $planModel->use - $curTime;
        if($check < 0){
            throw new OutOfTimeException();
        }
        return [$planModel, $curTime];

    }

    /**
     * todo 如果已经存在上次记录, 由于同一个"已完成"多次重复提交, 会导致重复计算, 必须先将上次的影响归零
     * @param $model  如果存在, 如果不存在
     * @return float|int
     */
    public function reCalPlan($model, $curTime, $planModel){
        if($model){
            $t1 = $model->t1;
            $t2 = $model->t2;
            $lastTime = ceil((strtotime($t2) - strtotime($t1))/ Params::ChannelTime);
        }else{
            //todo 如果是第一次, 那么上次就为0
            $lastTime = 0;
        }
        $planModel->update([
            'use' => $planModel->use + $curTime - $lastTime
        ]);
        return null;
    }
}