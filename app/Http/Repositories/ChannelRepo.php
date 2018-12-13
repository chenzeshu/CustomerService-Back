<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 8:48
 */

namespace App\Http\Repositories;


use App\Exceptions\Channels\OutOfTimeException;
use App\Exceptions\SP\ChannelNoCheckerExp;
use App\Exceptions\SP\ChannelProcessingExp;
use App\Http\Helpers\Params;
use App\Models\Channels\Channel;
use App\Models\Channels\Contractc_plan;
use App\Models\Contractc;

class ChannelRepo extends CurdRepo
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

    /**
     *  校验套餐的余量, 但不做减少, 直到实际运行才减少
     * @param $contractc_plan_id  信道套餐id
     * @param $t1 申请信道 : 开始时间  非时间戳
     * @param $t2 申请信道 : 结束时间
     * @return array
     * @throws OutOfTimeException
     */
    public function checkPlan($contractc_plan_id, $t1, $t2){
        $planModel = Contractc_plan::findOrFail($contractc_plan_id);
        $curTime = ceil((strtotime($t2) - strtotime($t1))/ Params::ChannelTime);
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

    /**
     * 检查本合同上次服务是否有人负责(无论是否临时, 若本次为第一次, 则无碍)
     * @param int $contractc_id 信道合同id
     */
    public function checkChannel($contractc_id)
    {
        $model = Contractc::findOrFail($contractc_id)->channels()->orderBy('created_at', 'desc')->first();

        if(empty($model)){ //本合同下第一次
            return true;
        }
        $data = $model->channel_applys()->orderBy('created_at', 'desc')->first()->channel_real()->first();
        if(!$data){
            throw new ChannelProcessingExp();
        }
        if($data->checker == ""){
            throw new ChannelNoCheckerExp();
        }
        return true;
    }

    /**
     * 将 "[2018年, 1月, 1日, 0点, 0分]" 数组格式 转换为 "2018-1-1 0:0"格式
     * @param $time
     * @return mixed
     */
    public function transformTimeFormat($time)
    {
        $time = str_replace("年", "-", $time);
        $time = str_replace("月", "-", $time);
        $time = str_replace("日", " ", $time);
        $time = str_replace("点", ":", $time);
        $time = str_replace("分", "", $time);
        return $time;
    }

    /**
     * 用于show方法的with数组
     * @param $status
     * @return array
     */
    public function getShowWithArr($status)
    {
        return ['contractc',
            'employee',
            'channel_applys' => function($query) use ($status){
                $arr = ['channel_relations.device.company',
                    'contractc_plan.plan'];
                switch ($status){
                    case "运营调配":
                        $arr = array_merge($arr, ['channel_operative' => function($query){
                            return $query->with(['tongxin', 'pinlv', 'jihua']);
                        }]);
                        break;
                    case "已完成" || "申述中":
                        $arr = array_merge($arr, ['channel_real'=> function($query){
                            return $query->with(['tongxin', 'pinlv', 'jihua','checker']);
                        }]);
                        break;
                    default: //待审核/拒绝
                        break;
                }
                return $query->with($arr);
            }];
    }

}