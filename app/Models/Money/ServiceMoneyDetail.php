<?php

namespace App\Models\Money;

use App\Observers\OBTraits\ContractUpdated;
use Illuminate\Database\Eloquent\Model;

class ServiceMoneyDetail extends Model
{
    use ContractUpdated;
    static $recordEvents = ['created', 'updated', 'deleted'];
    protected $guarded = [];
}
