<?php

namespace App\Models\Channels;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Channel_apply extends Model
{
    protected $guarded = [];

    /***** cache *****/
    static function get_cache(){
        $tongxin = Cache::get('tongxins');
        $jihua = Cache::get('jihuas');
        $daikuan = Cache::get('daikuans');
        return [$tongxin, $jihua, $daikuan];
    }

    /***** la-sql *****/
    static function basic_search(){
        return Channel::where('status','=','待审核');
    }

    static function get_pagination($begin, $pageSize){
        return static::basic_search()->offset($begin)
            ->limit($pageSize)
            ->with(['employee.company' ,'contractc'=>function($query){
                return $query->where('name', "!=", '临时合同');
            },
                'channel_applys.channel_relations.company',
                'channel_applys.channel_relations.device',
                'plans', 'tongxin','jihua', 'daikuan', 'source'])
            ->get()
            ->toArray();
    }

    static function get_temp_pagination($begin, $pageSize){
        return static::basic_search()->offset($begin)
            ->limit($pageSize)
            ->with(['employee.company' ,'contractc'=>function($query){
                return $query->where('name', "=", '临时合同');
            },
                'channel_applys.channel_relations.company',
                'channel_applys.channel_relations.device',
                'plans', 'tongxin','jihua', 'daikuan', 'source'])
            ->get()
            ->reject(function ($item){
                return $item->contractc == null;
            })
            ->toArray();
    }

    static function get_total(){
        return static::basic_search()->count();
    }

    /***** ORM *****/
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function channel_relations()
    {
        return $this->hasMany(Channel_relation::class);
    }
    
    public function channel_operative()
    {
        return $this->hasOne(Channel_operative::class);
    }

    public function channel_real()
    {
        return $this->hasOne(Channel_real::class);
    }
}
