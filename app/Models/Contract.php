<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $guarded = [

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class);
    }
}
