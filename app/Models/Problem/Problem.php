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
        return $this->hasManyThrough(
            Device::class,
            ProblemDevice::class,
            'problem_id',
            'id',
            'problem_id',
        'device_id');
    }

    /**
     * 得到具体的服务单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service(){
        return $this->hasOne(Service::class);
    }

    /**
     * 得到报警的具体时间记录（报警内容就是Problem表本身）
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problemRecord(){
        return $this->hasMany(ProblemRecord::class);
    }

    public function problemType()
    {
        return $this->belongsTo(ProblemType::class, 'problem_type', 'ptype_id')
            ->select(['ptype_id', 'ptype_name']);
    }
}
