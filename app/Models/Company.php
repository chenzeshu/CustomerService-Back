<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_nums;
use App\Models\Services\Service;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded  = [

    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function contract_cs()
    {
        return $this->hasMany(Contract_C::class);
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, Contract::class);
    }

    public function channels()
    {
        return $this->hasManyThrough(Channel::class, Contract_C::class);
    }

}

