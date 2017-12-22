<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Channels\Channel_plan;
use App\Models\Channels\Contractc_plan;
use App\Models\Money\ChannelMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Contractc extends Model
{
    protected $guarded = [];

    /**
     * 拿到utils缓存
     * $coors 合作商
     * $types 合同类型
     */
    static function get_cache(){

    }


    /***** 缓存时代 *****/
    static function redis_refresh_data(){
        Cache::forget('contractcs');
        $data = Contractc::orderBy('id', 'desc')
            ->with([
                'company',
                'ChannelMoney'=>function($query) {
                    $query->with([
                        'ChannelMoneyDetails',
                        'checker',
                    ]);
                },
            ])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
        Cache::put('contractcs', $data, 86400);  //每天无外力下自行更新一次
    }

    static function forget_cache(){
        Cache::forget('contractcs');
    }

    /***** la-sql *****/
    /**
     * @param String $finish 合同款项是否结清
     * @return $consModel 基本的[集合], 方便分页和总数的获取
     */
    static function basic_search($finish)
    {
        $consModel = Contractc::orderBy('id', 'desc')
            ->with([
                'company',
                'ChannelMoney'=>function($query) use ($finish){
                    if($finish != ""){
                        $query->where('finish', $finish);
                    }
                    $query->with([
                        'ChannelMoneyDetails',
                        'checker',
                    ]);
                },
            ])
            ->get()
            ->reject(function ($value, $key){
                return $value->ChannelMoney == null;
            });
        return $consModel;
    }

    /**
     *  分页数据
     * @param String $finish 合同款项是否结清
     */
    static function get_pagination($finish, $begin, $pageSize){
        return static::basic_search($finish)
            ->splice($begin, $pageSize)
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
    }

    /**
     * 分页: 拿到total总数
     * @param $finish
     */
    static function get_total($finish){
        return static::basic_search($finish)->count();
    }

    /*****  ORM  *****/
    //查该信道合同下的信道服务单
    public function channels()
    {
        return $this->hasMany(Channel::class);
    }

    //查该信道合同下的信道套餐
    public function channel_plans()
    {
        return $this->hasMany(Channel_plan::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * 到款情况
     */
    public function ChannelMoney()
    {
        return $this->hasOne(ChannelMoney::class);
    }

    /**
     * 得到名下套餐的使用情况
     */
    public function contractc_plans()
    {
        return $this->hasMany(Contractc_plan::class);
    }
}
