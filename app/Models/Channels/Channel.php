<?php

namespace App\Models\Channels;

use App\Models\Contractc;
use App\Models\Employee;
use App\Models\Utils\Plan;
use App\Models\Utils\Service_source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Channel extends Model
{


    protected $guarded = [

    ];

    /** 事件 **/
    protected $observers;


    /** 辅助函数们 */
    /**
     * 拿到工具表缓存
     * @return array
     */
    static function get_cache(){
        $cache = Cache::many(['service_sources', 'tongxins', 'jihuas', 'plans', 'zhantypes']);
        return $cache;
    }

    /***** 缓存时代 *****/
    static function redis_refresh_data(){
        Cache::forget('channels');
        $channels = Channel::where('status', "!=", '待审核')
            ->with([
                'contractc',
                'channel_applys'=>function($query){
                        $query->with([
                            "channel_relations"=>function($re){
                                $re->with(["company", "device"]);
                            },
                            "channel_operative"=>function($op){
                                $op->with([ "tongxin","jihua","pinlv","plan"]);
                            },
                            "channel_real"=>function($real){
                                $real->with(["tongxin","jihua","pinlv","checker"]);
                            },
                        ]);
                    }
            ])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->customer = $item->employee_id == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->employee_id})");
                $item->source_info = $item->source == null ? null : DB::select("select `id`, `name` from service_sources where id = {$item->source}");
                return $item;
            })
            ->toArray();
          Cache::put('channels', $channels, 86400);
    }

    static function forget_cache(){
        Cache::forget('channels');
    }
    /**
     *  分页基本筛选
     */
    static function basic_search($status){
        return Channel::where('status', "!=", '待审核')
                       ->where(function ($query) use ($status){
                            if($status != ""){
                                $query->where('status', $status);
                            }
                       });
    }

    /**
     * 分页: 拿到符合筛选条件的分页数据
     * @status 外部检索的状态
     * @begin offset
     * @pageSize 单页数据量
     */
    static function get_pagination($status, $begin, $pageSize){
        return static::basic_search($status)
            ->orderBy('updated_at', 'desc')
            ->offset($begin)
            ->limit($pageSize)
            ->with([
                'contractc',
                'channel_applys'=>function($query){
                    $query->with([
                        "channel_relations"=>function($re){
                            $re->with(["company", "device"]);
                        },
                        "channel_operative"=>function($op){
                            $op->with([ "tongxin","jihua","pinlv","plan"]);
                        },
                        "channel_real"=>function($real){
                            $real->with(["tongxin","jihua","pinlv","checker"]);
                        },
                    ]);
                }
            ])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->customer = $item->employee_id == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->employee_id})");
                $item->source_info = $item->source == null ? null : DB::select("select `id`, `name` from service_sources where id = {$item->source}");
                return $item;
            })
            ->toArray();
    }

    /**
     * 分页: 拿到符合筛选条件的总数
     * @status 外部检索的状态
     */
    static function get_total($status){
        $total = static::basic_search($status)->count();
        return $total;
    }

    /*****    ORM    *****/
    public function channel_applys()
    {
        return $this->hasMany(Channel_apply::class);
    }

    public function contractc()
    {
        return $this->belongsTo(Contractc::class);
    }

    //人可以查到名下的所有信道服务单
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function source()
    {
        return $this->hasOne(Service_source::class, 'id', 'source');
    }

    /**
     * 信道服务单通过apply远层关联套餐
     */
    public function plans()
    {
        return $this->hasManyThrough(
            Plan::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id1');
    }

    /**
     * 通过apply远层通信卫星
     */
    public function tongxin()
    {
        return $this->hasManyThrough(
            Channel_info3::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id2');
    }

    /**
     * 通过apply远层极化
     */
    public function jihua()
    {
        return $this->hasManyThrough(
            Channel_info5::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id3');
    }

    /**
     * 通过apply层带宽
     */
    public function daikuan()
    {
        return $this->hasManyThrough(
            Channel_info1::class,
            Channel_apply::class,
            'channel_id',
            'id',
            'id',
            'id4');
    }
}
