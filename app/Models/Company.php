<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_nums;
use App\Models\Channels\Channel_plan;
use App\Models\Services\Service;
use App\Models\Utils\Device;
use App\Models\Utils\Profession;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded  = [

    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    //每个出库的设备必然有他的母体单位
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function contract_cs()
    {
        return $this->hasMany(Contract_c::class);
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, Contract::class);
    }

    //通过信道合同查到单位名下的所有信道服务单
    public function channels()
    {
        return $this->hasManyThrough(Channel::class, Contract_C::class);
    }

    //通过信道合同查到单位名下的所有信道套餐
    public function channel_plans()
    {
        return $this->hasManyThrough(Channel_plan::class, Contract_C::class);
    }

    public function professions()
    {
        return $this->hasMany(Profession::class);
    }
}

