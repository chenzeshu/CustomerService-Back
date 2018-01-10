<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 11:32
 */

namespace App\Http\Repositories;


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
            $model = $parentModel->contract_plans()->findOrFail($parentModel->type);
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
            $model = $parentModel->contract_plans()->findOrFail($parentModel->type);
            $model->update([$column => $model->use - $num]);
        } catch (ModelNotFoundException $e) {
            //假如服务单是刚创建, 没有人工去选套餐就要删除的话, 就会找不到contract_plans
            //那么就直接跳过
            return;
        }
    }
}