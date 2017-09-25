<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_plan;
use Illuminate\Database\Eloquent\Model;

class Contract_c extends Model
{
    protected $guarded = [];

    //查该信道合同下的信道服务单
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    //查该信道合同下的信道套餐
    public function channel_plans()
    {
        return $this->hasMany(Channel_plan::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}
