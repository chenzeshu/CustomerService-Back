<?php

namespace App\Models\Services;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Money\ServiceMoney;
use App\Models\Utils\Service_source;
use App\Models\Utils\Service_type;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [ ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function company()
    {
        return $this->hasManyThrough(Company::class, Contract::class, 'id', 'id', 'contract_id', 'company_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function refer_man()
    {
        return $this->hasMany(Employee::class, 'id','refer_man')->select(['id', 'name', 'phone']);
    }

    /**
     * ORM对应客户联系人
     */
    public function customer()
    {
        return $this->hasMany(Employee::class, 'id', 'customer');
    }

    /**
     * 服务类型
     */
    public function source()
    {
        return $this->hasMany(Service_source::class, 'id', 'source');
    }

    /**
     * 服务来源
     */
    public function type()
    {
        return $this->hasMany(Service_type::class, 'id', 'type');
    }
}
