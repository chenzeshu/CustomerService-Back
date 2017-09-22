<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = [

    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
