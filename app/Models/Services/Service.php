<?php

namespace App\Models\Services;

use App\Models\Company;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [

    ];

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
}
