<?php

namespace App\Models\Channels;

use Illuminate\Database\Eloquent\Model;

class Contractc_plan extends Model
{
    //use ContractcUpload;
    protected $guarded = [];

    /**
     * 得到相关的套餐信息
     */
    public function plan()
    {
        return $this->hasOne(Channel_plan::class, 'id', 'plan_id');
    }
}
