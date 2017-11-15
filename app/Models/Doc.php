<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at'
    ];
}
