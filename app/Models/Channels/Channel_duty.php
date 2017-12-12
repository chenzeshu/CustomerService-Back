<?php

namespace App\Models\Channels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Channel_duty extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    static function getData($page, $pageSize){
        $begin = ($page - 1) * $pageSize;
        $model = Channel_duty::offset($begin)
            ->limit($pageSize)
            ->orderBy('id', 'desc')
            ->get()
            ->each(function ($item){
                $item->checker = $item->employee_id == null ? null : DB::select("select `id`, `name` from employees where id = {$item->employee_id}");
                $item->checker = $item->checker[0];
            })
            ->toArray();
        $total = Channel_duty::count();
        return [
            'data' => $model,
            'total'=>$total
        ];
    }
}
