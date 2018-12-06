<?php

namespace App\Models\Utils;

use App\Models\Channels\Channel_info2;
use App\Models\Company;
use App\Models\Problem\Problem;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    //站类型ORM
    public function channel_info2()
    {
        return $this->belongsTo(Channel_info2::class, 'id5', 'id');
    }

    public function problems()
    {
        return $this->belongsToMany(
            Problem::class,
            'problem_devices',
            'device_id',
            'problem_id');
    }
}
