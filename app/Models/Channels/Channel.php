<?php

namespace App\Models\Channels;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $guarded = [

    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
