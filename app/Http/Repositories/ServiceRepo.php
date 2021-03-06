<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 11:32
 */

namespace App\Http\Repositories;


use App\Exceptions\Services\BelowZeroException;
use App\Exceptions\Services\TooMuchUseException;
use App\Models\Problem\Problem;
use App\Models\Services\Contract_plan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ServiceRepo
{
    /**
     * 利用model::update实现的自增, 目的是触发saved模型事件
     * @param $parentModel ORM父表模型
     * @string $column 字段名
     * @param $num 增加数目
     * @param $contract_plan_id 在审核待审核服务单时，其模型不具有`contract_plan_id`，则需要外部传入
     */
    public function myIncrement($parentModel, $column, $num, $contract_plan_id = "")
    {
        if($contract_plan_id){
            $model = Contract_plan::findOrFail($contract_plan_id);
        } else {
            $model = Contract_plan::findOrFail($parentModel->contract_plan_id);
        }

        //todo 做次数是否大于total 审核
        if($model->use + $num > $model->total){
            throw new TooMuchUseException();
        }
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
            //todo 做次数是否小于0审核
            if($model->use - $num < 0){
                throw new BelowZeroException();
            }
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

    /**
     * 服务单页面新建故障及故障设备
     * @param $problem_data
     * @param $device_ids
     */
    public function synchronize_problem($problem_data, $device_ids)
    {
        DB::transaction(function () use ($problem_data, $device_ids){
            $problem = Problem::create($problem_data);
            $problem->devices()->attach($device_ids);
        });
    }

    /**
     * 服务单页面更新故障及故障设备
     * @param $problem_data
     * @param $device_ids
     */
    public function update_problem($problem_data, $device_ids)
    {
        DB::transaction(function () use ($problem_data, $device_ids){
            $problem = Problem::findOrFail($problem_data['problem_id']);
            $problem->update($problem_data);
            $problem->devices()->sync($device_ids);
        });
    }
}