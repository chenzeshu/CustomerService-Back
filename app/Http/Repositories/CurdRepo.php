<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/19
 * Time: 17:41
 */

namespace App\Http\Repositories;


use App\Id_record;

class CurdRepo
{
    /**
     * 生成单的编号
     * [1=>销字合同计数, 2=>客字合同计数, 3=>信合计数, 4=>客服, 5=>信服]
     * @return  $channel_id 新编号
     * @return  $recordModel 返回模型便于自增
     * fixme 重构时可将自增放入此方法, 并仅将此方法在事务中调用
     */
    public function generateNumber($number)
    {
        $recordModel = Id_record::find($number);
        $record = $recordModel->record;
        $len = 3 - strlen($record);
        $channel_id = date('Y', time()).zerofill($len).$record;
        return [$recordModel, $channel_id];
    }
}