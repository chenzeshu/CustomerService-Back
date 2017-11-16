<?php

namespace App\Models\Services;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $guarded = [

    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'id', 'visitor');
    }
}
