<?php

namespace App\Models\Problem;

use App\Models\Services\Service;
use App\Models\Utils\Device;
use Illuminate\Database\Eloquent\Model;

/**
 * 具体故障表
 * Class Problem
 * @package App\Models\Problem
 */
class Problem extends Model
{
    protected $primaryKey = 'problem_id';

    protected $guarded = [];

    /**
     * 得到具体问题对应的设备
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function devices(){
        return $this->belongsToMany(
            Device::class,
            'problem_devices',
            'problem_id',
            'device_id');
    }

    /**
     * 得到具体的服务单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service(){
        return $this->belongsTo(Service::class);
    }

    /**
     * 得到报警的具体时间记录（报警内容就是Problem表本身）
     * 对应中间表 problem_record
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reportRecords(){
        return $this->belongsToMany(Device::class, 'problem_records', 'problem_id', 'device_id');
    }

    public function problemType()
    {
        return $this->belongsTo(
            ProblemType::class,
            'problem_type',
            'ptype_id')
            ->select(['ptype_id', 'ptype_name']);
    }
}
