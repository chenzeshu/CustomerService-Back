<?php

namespace App\Models;

use App\Models\Channels\Channel;
use App\Models\Money\ServiceMoney;
use App\Models\Money\ServiceMoneyDetail;
use App\Models\Services\Contract_plan;
use App\Models\Services\Contract_planutil;
use App\Models\Services\Service;
use App\Models\Utils\Contract_type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Contract extends Model
{
    protected $guarded = [];

    /**
     * 拿到utils缓存
     * $coors 合作商
     * $types 合同类型
     */
    public static function get_cache(){
        $cache = Cache::many(['coors','contract_types', 'contract_plans']);
        return $cache;
    }

    /***** la-sql *****/
    /**
     * @param String $finish 合同款项是否结清
     * @return $consModel 基本的[集合], 方便分页和总数的获取
     */
    public static function basic_search($finish)
    {
        $consModel = Contract::orderBy('id', 'desc')
            ->with([
                'company',
                'ServiceMoney'=>function($query) use ($finish){
                    if($finish != ""){
                        $query->where('finish', $finish);
                    }
                    $query->with([
                        'ServiceMoneyDetails',
                        'checker',
                    ]);
                },
            ])
            ->get()
            ->reject(function ($value, $key){
                return $value->ServiceMoney == null;
            });
        return $consModel;
    }

    /***** 缓存低并发时代 隐患: 高并发时会脏读*****/
    //todo 拿到全部缓存  -- 相当于refresh  --但是实际在update下应该做成job
    public static function redis_refresh_data(){
        Cache::forget('contracts');
        $data = Contract::orderBy('id', 'desc')
            ->with([
                'company',
                'ServiceMoney'=>function($query){
                    $query->with([
                        'ServiceMoneyDetails',
                        'checker',
                    ]);
                },
                'Contract_plans.planUtil'
            ])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->TM = $item->TM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->TM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
        Cache::put('contracts', $data, 86400);  //每天无外力下自行更新一次
    }

    /**
     * 使缓存失效
     */
    public static function forget_cache(){
        Cache::forget('contracts');
    }

    /***** 不使用缓存的分页获取方法********/

    /**
     *  分页数据
     * @param String $finish 合同款项是否结清
     */
    public static function get_pagination($finish, $begin, $pageSize){
        return static::basic_search($finish)
            ->splice($begin, $pageSize)
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->TM = $item->TM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->TM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
    }

    /**
     * @param String $finish 合同款项是否结清
     */
    public static function get_total($finish){
        return static::basic_search($finish)->count();
    }

    /*****  ORM *****/
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * 到款情况
     */
    public function ServiceMoney()
    {
        return $this->hasOne(ServiceMoney::class);
    }

    /**
     * 到款细节
     */
    public function ServiceMoneyDetails()
    {
        return $this->hasManyThrough(ServiceMoneyDetail::class,
                    ServiceMoney::class,
                    'contract_id',
                    'service_money_id',
                    'id',
                    'id'
                );
    }

    /**
     * 一合同对多套餐, 留着配合多对多补全with方法
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Contract_plans()
    {
        return $this->hasMany(Contract_plan::class);
    }

    /**
     * 多对多, 便于利用 laravel内置attach 和 detach
     * 但由于bug不能使用 with ,只有配合上方的`Contract_plans()`了
     */
    public function con_plans()
    {
        return $this->belongsToMany(Contract_plan::class, 'contract_plans', 'contract_id', 'plan_id');
    }

    public function planUtils()
    {
        return $this->hasManyThrough(Contract_planutil::class,
            Contract_plan::class,
            'contract_id',       //中间表内连接起源表的键
            'id',             //终点表内连接中间表的键
            'id',               //起源表内连接中间表的键
            'plan_id');  //中间表内连接终点表的键
    }
}
