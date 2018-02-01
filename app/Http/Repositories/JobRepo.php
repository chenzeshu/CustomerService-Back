<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/1
 * Time: 11:14
 */

namespace App\Http\Repositories;


use App\Models\Services\Service;

class JobRepo
{
    /**
     * 检查申请查看者是否与服务单有关
     * @array service  服务model
     * @string emp_id  申请查看者id
     */
    public function filterRelation($service, $emp_id)
    {
        $arr = $this->packRelationArr($service);
        foreach ($arr as $a){
            if($a == $emp_id){
                return true;
            }
        }
        unset($a);
        return false;
    }

    /**
     * 组装filter数组
     * @param service 服务单信息
     * @param arr 人员id数组 ---- 客户, 服务人员, 项目经理, 技术经理, 申请人，回访人
     */
    public function packRelationArr($service)
    {
        $list = "";
        if($service['contract']['TM']){
            $list .= $service['contract']['TM'] .",";
        }
        if($service['man']){
            $list .= $service['man'] .",";
        }
        if($service['visits']){
            $list .= $service['visits'][0]['visitor']. ",";
        }
        $list .= $service['contract']['PM'] . "," . $service['contract']['TM'] . "," . $service['customer'] . "," . $service['refer_man'];
        $list = rtrim(ltrim($list, "," ),  "," );
        $arr = explode(",", $list);

        return $arr;
    }
}