<?php

namespace App\Models\Channels;

use App\Models\Employee;
use App\Models\Utils\Plan;
use Illuminate\Database\Eloquent\Model;

class Channel_real extends Model
{
    protected $guarded = [];

    public function channel_apply()
    {
        return $this->belongsTo(Channel_apply::class);
    }

    public function checker()
    {
        return $this->hasOne(Employee::class, 'id', 'checker_id');
    }

    public function tongxin()
    {
        return $this->hasOne(Channel_info3::class, 'id', 'id3');
    }

    public function pinlv()
    {
        return $this->hasOne(Channel_info4::class, 'id', 'id5');
    }

    public function jihua()
    {
        return $this->hasOne(Channel_info5::class, 'id', 'id4');
    }
}
