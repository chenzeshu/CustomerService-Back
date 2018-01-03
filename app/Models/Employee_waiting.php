<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee_waiting extends Model
{
    protected $guarded = [];

    public function errnos()
    {
        return $this->hasMany(Employee_errno::class);
    }
}
