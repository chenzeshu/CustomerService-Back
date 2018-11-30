<?php

namespace App\Models\Services;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Problem\Problem;
use App\Models\Utils\Service_source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    protected $guarded = [ ];

    /**
     * 拿到utils缓存
     * $coors 合作商
     * $types 合同类型
     */
    static function get_cache(){
        $types = Cache::get('service_types');
        $sources = Cache::get('service_sources');
        return [$types, $sources];
    }

    /***** la-sql *****/
    /**
     * @param String $status 服务单状态
     * @param String $charge_flag 是否到款
     * @return $consModel 基本的[集合], 方便分页和总数的获取
     */
    static function basic_search($status, $charge_flag)
    {
        $services = Service::where('status', "!=", '待审核')
            ->where(function ($query) use ($status, $charge_flag){
                if($status != ""){
                    $query->where('status', $status);
                }
                if($charge_flag != ""){
                    $query->where([
                        'charge_if'=>'收费',
                        'charge_flag'=> $charge_flag
                    ]);
                }
            });
        return $services;
    }

    /**
     *  分页数据
     * @param String $status 服务单状态
     * @param String $charge_flag 是否到款
     */
    static function get_pagination($status, $charge_flag, $begin, $pageSize){
        return static::basic_search($status, $charge_flag)
            ->orderBy('updated_at', 'desc')
            ->offset($begin)
            ->limit($pageSize)
            ->with([
                'contract'=>function($query){
                    return $query->with([
                       'company', 'planUtils'
                    ]);
                },
                'problem.device',
                'visits.employees',
                'refer_man'])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(本次为配合前端提前做的search组件 -> 下次考虑聚合, 不做层级了)
                $item->man = $item->man == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->man})");
                $item->customer = $item->customer == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->customer})");
                $item->refer_man = $item->refer_man == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->refer_man})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
    }

    /**
     * 分页: 拿到total总数
     */
    static function get_total($status, $charge_flag){
        return static::basic_search($status, $charge_flag)->count();
    }

    /*****  ORM  *****/
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function company()
    {
        return $this->hasManyThrough(Company::class, Contract::class, 'id', 'id', 'contract_id', 'company_id');
    }

    /**
     * 回访人
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /**
     *  服务单提交人
     */
    public function refer_man()
    {
        return $this->hasMany(Employee::class, 'id','refer_man')->select(['id', 'name', 'phone','company_id']);
    }

    /**
     * ORM对应客户联系人
     */
    public function customer()
    {
        return $this->hasMany(Employee::class, 'id', 'customer');
    }

    /**
     * 服务类型
     */
    public function source()
    {
        return $this->hasMany(Service_source::class, 'id', 'source');
    }

    /**
     * 服务来源
     */
    public function type()
    {
        return $this->hasOne(Contract_planutil::class, 'id', 'type');
    }

    /**
     * 服务对应的合同的套餐的使用情况
     * 只检索id, plan_id, total和use字段
     */
    public function contract_plans()
    {
        return $this->hasMany(Contract_plan::class,
            'contract_id',
            'contract_id')
            ->select('plan_id','total', 'use', 'id');
    }

    /**
     * 一个服务只会对应一个套餐，而一个套餐（详情）对应若干个服务
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function contract_plan_detail()
    {
        return $this->hasOne(Contract_plan::class,
            'id',
            'contract_plan_id')
            ->select('plan_id','total', 'use', 'id');
    }

    public function problem(){
        return $this->belongsTo(Problem::class, 'id', 'service_id');
    }
}
