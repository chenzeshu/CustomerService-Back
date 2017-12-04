<?php

namespace App\Models\Money;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class ChannelMoney extends Model
{
    protected $guarded = [];

    public function ChannelMoneyDetails()
    {
        return $this->hasMany(ChannelMoneyDetail::class);
    }

    public function checker()
    {
        return $this->hasOne(Employee::class, 'id', 'checker_id');
    }

}
