<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/4
 * Time: 11:28
 */

namespace App\Dao;

use App\Id_record;
use App\Models\Employee;
use App\Models\Services\Service;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceDAO
{
    /**
     * 取得与前端请求人员有关的服务信息
     * @param $begin 开始页数
     * @param $emp_id 人员id
     * @return mixed
     */
    public static function getService($page, $pageSize, $emp_id, $status)
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
        Log::info($status.PHP_EOL);
        if($status !== "全部"){
            $data = collect($data)->filter(function($item) use ($status){
                return $item->status == $status;
            })->toArray();
        }

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


    /**
     * 员工报修
     * @param $service_id  服务单的编号
     * @param $request
     * @return mixed
     */
    public static function empCreate($request)
    {
        $service_id = self::generateId();
        return Service::create([
            'contract_id' => $request->contract_id,
            'service_id' => $service_id,
            'type' => $request->type_id,
            'customer'=> $request->cus_id,
            'refer_man' => $request->zhongId,
            'source'=>4
        ]);
    }

    /**
     * 客户报修(则申请人与客户为同一个人)
     * @param $service_id  服务单的编号
     * @param $request
     * @return mixed
     */
    public static function cusCreate($request)
    {
        $service_id = self::generateId();
        return Service::create([
            'contract_id' => $request->contract_id,
            'service_id' => $service_id,
            'type' => $request->type_id,
            'customer'=> $request->cus_id,
            'refer_man' => $request->cus_id,
            'source'=>4
        ]);
    }

    /**
     * 生成service单号
     */
    private static function generateId(){
        //todo 自动生成服务单编号
        $record = Id_record::find(4)->record;
        $len = 3 - strlen($record);
        return  date('Y', time()).zerofill($len).$record;
    }

    /**
     * 得到服务单状态(二维数组{id:xx, status:xx})
     * @return \Illuminate\Config\Repository|mixed
     */
    public static function getServiceStatus(){
        $status = Cache::get('service_status');
        if(empty($status)){
            $status = config('app.status');
            $type = [];
            foreach ($status as $k=>$s){
                $type[] = ['id' =>++$k, 'status'=>$s];
            }
            Cache::put('service_status', $type, 86400);
        }
        return $status;
    }
    
    /**
     * 得到服务单类型
     */
    public static function getServiceTypes()
    {
        $service_types = Cache::get("service_types");
        return $service_types;
    }
}