<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 16:10
 */

namespace App\Http\Repositories;


class ContractcRepo
{
    public function pageFilter($cons, $finish, $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $data = collect($cons)->reject(function($item) use ($finish){
            if($finish != ""){
                return $item['channel_money']['finish'] != $finish;
            }
        });
        $total = count($data);
        $data = $data->splice($begin, $pageSize);
        return [$data, $total];
    }
}