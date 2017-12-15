<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;

//服务合同套餐表(外键表)
class Contract_plan extends Model
{
    protected $guarded = [];
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function planUtil()
    {
        return $this->hasOne(Contract_planutil::class, 'id', 'plan_id');
    }
}
