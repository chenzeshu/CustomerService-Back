<?php

namespace App\Models\Channels;

use App\Models\Contractc;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [

    ];

    public function channel_applys()
    {
        return $this->hasMany(Channel_apply::class);
    }

    public function contract_c()
    {
        return $this->belongsTo(Contractc::class);
    }

    //人可以查到名下的所有信道服务单
    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }
}
