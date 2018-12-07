<?php

namespace App\Models\Problem;

use App\Models\Utils\Device;
use Illuminate\Database\Eloquent\Model;

/**
 * 故障报警表
 * Class ProblemRecord
 * @package App\Models\Problem
 */
class ProblemRecord extends Model
{
    protected $primaryKey = 'precord_id';

    protected $guarded = [];


    /**
     * 中间表拿到对应问题
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id', 'problem_id');
    }


    /**
     * 一条警报记录只对应一个设备（当然多条警报可能是一个预警=>多个设备产生的）
     */
    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
