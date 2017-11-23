<?php

namespace App\Models\Channels;

use App\Models\Utils\Plan;
use Illuminate\Database\Eloquent\Model;

class Channel_operative extends Model
{
    protected $guarded = [];

    public function channel_apply()
    {
        return $this->belongsTo(Channel_apply::class);
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'id', 'id1');
    }

    public function tongxin()
    {
        return $this->hasOne(Channel_info3::class, 'id', 'id2');
    }

    public function pinlv()
    {
        return $this->hasOne(Channel_info4::class, 'id', 'id4');
    }

    public function jihua()
    {
        return $this->hasOne(Channel_info5::class, 'id', 'id3');
    }


}
