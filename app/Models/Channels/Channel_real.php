<?php

namespace App\Models\Channels;

use Illuminate\Database\Eloquent\Model;

class Channel_real extends Model
{
    protected $guarded = [];

    public function channel_apply()
    {
        return $this->belongsTo(Channel_apply::class);
    }
}
