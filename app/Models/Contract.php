<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Money\ServiceMoney;
use App\Models\Money\ServiceMoneyDetail;
use App\Models\Services\Service;
use App\Models\Utils\Contract_type;
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

    /**
     * 到款情况
     */
    public function ServiceMoney()
    {
        return $this->hasOne(ServiceMoney::class);
    }

    /**
     * 到款细节
     */
    public function ServiceMoneyDetails()
    {
        return $this->hasManyThrough(ServiceMoneyDetail::class,
            ServiceMoney::class,
            'contract_id',
        'service_money_id',
        'id',
        'id'
            );
    }
}
