<?php

namespace App\Models\Channels;

use App\Models\Contract_C;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [

    ];

    public function channel_applys()
    {
        return $this->hasMany(Channel_apply::class);
    }

    public function contract_c()
    {
        return $this->belongsTo(Contract_C::class);
    }


}
