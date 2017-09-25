<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract_C extends Model
{
    protected $guarded = [];

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
