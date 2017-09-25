<?php

namespace App\Models\Channels;

use App\Models\Contract_c;
use Illuminate\Database\Eloquent\Model;

class Channel_plan extends Model
{
    protected $guarded = [];

    public function contract_c()
    {
        return $this->belongsTo(Contract_c::class);
    }

    //每次套餐使用的细节
    public function channel_details()
    {
        return $this->hasMany(Channel_detail::class);
    }

}
