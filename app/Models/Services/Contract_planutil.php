<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Model;

//工具表 用于维护服务合同的套餐
class Contract_planutil extends Model
{
    protected $guarded = [];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
