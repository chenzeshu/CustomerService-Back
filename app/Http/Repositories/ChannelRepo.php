<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/7
 * Time: 8:48
 */

namespace App\Http\Repositories;


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
}