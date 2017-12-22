<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 11:32
 */

namespace App\Http\Repositories;


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
        $model = $parentModel->contract_plans()->findOrFail($parentModel->type);
        $model->update([$column=> $model->use - $num]);
    }
}