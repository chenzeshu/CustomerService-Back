<?php

namespace App\Models\Utils;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
