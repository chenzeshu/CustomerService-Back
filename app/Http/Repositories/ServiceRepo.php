<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 11:32
 */

namespace App\Http\Repositories;


use App\Models\Services\Contract_plan;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServiceRepo
{
    /**
     * 利用model::update实现的自增, 目的是触发saved模型事件
     * @param $parentModel ORM父表模型
     * @string $column 字段名
     * @param $num 增加数目
     */
    public function myIncrement($parentModel, $column, $num)
    {
            $model = Contract_plan::findOrFail($parentModel->contract_plan_id);
            $model->update([$column => $model->use + $num]);
    }

    /**
     * 利用model::update实现的自减, 目的是触发saved模型事件
     * @param $parentModel ORM父表模型
     * @string $column 字段名
     * @param $num 减数目
     */
    public function myDecrement($parentModel, $column, $num)
    {
        try {
            //tips 一个服务对应一个套餐详情，一个类型对应多个套餐详情，一个套餐详情对应一个类型和多个服务
            $model = Contract_plan::findOrFail($parentModel->contract_plan_id);
            $model->update([$column => $model->use - $num]);
        } catch (ModelNotFoundException $e) {
            //假如服务单是刚创建, 没有人工去选套餐就要删除的话, 就会找不到contract_plans
            //那么就直接跳过
            return;
        }
    }

    /**
     * 筛选
     */
    public function filterData($data, $company_name, $emp_name, $service_id)
    {
        $data = collect($data);
        if($company_name != ""){
            $data = $this->filterCompanyName($data, $company_name);
        }
        if($emp_name != ""){
            $data = $this->filterEmpName($data, $emp_name);
        }
        if($service_id != ""){
            $data = $this->filterServiceId($data, $emp_name);
        }
        return $data->toArray();
    }

    public function filterCompanyName($data, $company_name)
    {
        return $data->filter(function($item) use ($company_name){
           if(strpos($item['contract']['company']['name'], $company_name)  !== false){
               return true;
           }
        });
    }

    public function filterEmpName($data, $emp_name)
    {
        return $data->filter(function($item) use ($emp_name){
            $item = collect($item['man'])->filter(function($man) use ($emp_name){
                if(strpos($man->name, $emp_name) !== false){
                    return true;
                }
            });
            if(count($item) > 0){
                return true;
            }
        });
    }

    public function filterServiceId($data, $service_id)
    {
        return $data->filter(function($item) use ($service_id){
            if($item['service_id'] === $service_id){
                return true;
            }
        });
    }
}