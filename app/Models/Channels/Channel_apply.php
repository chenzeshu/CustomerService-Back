<?php

namespace App\Models\Channels;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Channel_apply extends Model
{
    protected $guarded = [];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function channel_relations()
    {
        return $this->hasMany(Channel_relation::class);
    }
    
    public function channel_operatives()
    {
        return $this->hasMany(Channel_operative::class);
    }

    public function channel_real()
    {
        return $this->hasMany(Channel_real::class);
    }
}
