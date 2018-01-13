<?php

namespace App\Models\Channels;

use App\Observers\OBTraits\ContractcUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Contractc_plan extends Model
{
    use ContractcUpdated;
    static $recordEvents = ['created', 'updated', 'deleted', 'saved'];
    protected $guarded = [];

    /**
     * 得到相关的套餐信息
     */
    public function plan()
    {
        return $this->hasOne(Channel_plan::class, 'id', 'plan_id');
    }
}
