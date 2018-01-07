<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/4
 * Time: 11:28
 */

namespace App\Dao;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class ServiceDAO
{
    /**
     * 取得与前端请求人员有关的服务信息
     * @param $begin 开始页数
     * @param $emp_id 人员id
     * @return mixed
     */
    public static function getService($page, $pageSize, $emp_id)
    {
        $begin = ($page - 1) * $pageSize;
        $data = DB::select("SELECT s.id, s.service_id, s.status, s.charge_if, s.time1, s.time2 ,s.man, s.customer as customer_id,
        c.name, c2.name as customer, c3.name as type
        FROM services as s 
        LEFT JOIN employees as c on c.id in (s.man) 
        LEFT JOIN employees as c2 on c2.id = s.customer
        LEFT JOIN service_types as c3 on c3.id = s.type
        where find_in_set('$emp_id', s.man) 
        ORDER BY s.time1 desc
        LIMIT $begin, $pageSize");

        $len = count($data);
        if($len >= 1){
            collect($data)->map(function ($d){
                $company = Employee::with('company')->where('id', $d->customer_id)->get();
                $d->company = collect($company[0]['company'])->except(['created_at', 'updated_at', 'profession']);
                if(strpos($d->man, ',')){
                    $arr = DB::select("select name from employees where id in (".$d->man.")");

                    $d->name = collect($arr)->implode('name', ",");

                }
            });
        }

        return $data;
    }
}