<?php

namespace App\Models\Money;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class ServiceMoney extends Model
{
    protected $guarded = [];

    public function ServiceMoneyDetails()
    {
        return $this->hasMany(ServiceMoneyDetail::class);
    }

    public function checker()
    {
        return $this->hasOne(Employee::class, 'id', 'checker_id');
    }
}
