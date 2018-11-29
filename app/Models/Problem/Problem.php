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
    public function device(){
        return $this->hasOne(Device::class);
    }

    /**
     * 得到具体的服务单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service(){
        return $this->hasOne(Service::class);
    }

    /**
     * 得到问题记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problems(){
        return $this->hasMany(Problem::class);
    }

    /**
     * 得到本故障的报警记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problemRecord(){
        return $this->hasMany(ProblemRecord::class);
    }
}
