<?php

namespace App\Models\Services;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [

    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function visit()
    {
        return $this->hasMany(Visit::class);
    }
}
