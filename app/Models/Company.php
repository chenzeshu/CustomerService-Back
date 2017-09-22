<?php

namespace App\Models;

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

    public function services()
    {
        return $this->hasManyThrough(Service::class, Contract::class);
    }

    public function channels()
    {
        return $this->hasManyThrough(Channel::class, Contract::class);
    }
}

