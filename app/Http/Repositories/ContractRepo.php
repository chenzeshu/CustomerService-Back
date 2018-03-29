<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/6
 * Time: 12:52
 */

namespace App\Http\Repositories;


use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /**
     * 临时用, 以后肯定要全部搞到DAO里去
     * @param $sc
     * @return array
     */
    public function newPageRepo($sc)
    {
        $page = $sc['page'];
        $pageSize = $sc['pageSize'];
        $begin = (int)(($page - 1) * $pageSize);

        $cons  = Contract::with([
            'company',
            'ServiceMoney'=>function($query){
                $query->with([
                    'ServiceMoneyDetails',
                    'checker',
                ]);
            },
            'Contract_plans.planUtil']);
        $count = $cons->count();
        $data =  $cons
            ->orderBy('id', 'desc')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->TM = $item->TM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->TM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->reject(function($con) use ($sc){
                return $con->company_id !== $sc['company_id'];
            })
            ->toArray();
        return [$data, $count];
    }
}