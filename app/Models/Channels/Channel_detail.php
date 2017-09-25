<?php

namespace App\Models\Channels;

use Illuminate\Database\Eloquent\Model;

class Channel_detail extends Model
{
    protected $guarded = [];

    public function channel_plan()
    {
        return $this->belongsTo(Channel_plan::class);
    }
}
