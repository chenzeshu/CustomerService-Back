<?php

namespace App\Models\Channels;

use App\Models\Company;
use App\Models\Utils\Device;
use Illuminate\Database\Eloquent\Model;

class Channel_relation extends Model
{
    protected $guarded = [];

    public function channel_apply()
    {
        return $this->belongsTo(Channel_apply::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function device()
    {
        return $this->hasOne(Device::class, 'id', 'device_id');
    }
}
