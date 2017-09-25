<?php

namespace App\Models;

use App\Models\Channels\Channel;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    //查人物下的信道服务单, 方便调取服务单状态
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
