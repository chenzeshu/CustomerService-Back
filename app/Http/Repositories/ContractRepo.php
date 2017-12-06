<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 12:52
 */

namespace App\Http\Repositories;


class ContractRepo
{
    /**
     * 用于筛选复合$finish -- 合同是否结清的数据
     */
    public function pageFilter($cons, $finish, $page, $pageSize){
        $begin = ( $page -1 ) * $pageSize;
        $data = collect($cons)->reject(function($item) use ($finish){
            if($finish != ""){
                return $item['service_money']['finish'] != $finish;
            }
        });
        $total = count($data);
        $data = $data->splice($begin, $pageSize);
        return [$data, $total];
    }
}