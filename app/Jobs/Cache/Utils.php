<?php

namespace App\Jobs\Cache;

use App\Models\Channels\Channel_info1;
use App\Models\Channels\Channel_info3;
use App\Models\Channels\Channel_info5;
use App\Models\Channels\Channel_info2;
use App\Models\Utils\Contract_type;
use App\Models\Utils\Coor;
use App\Models\Utils\Plan;
use App\Models\Utils\Profession;
use App\Models\Utils\Service_source;
use App\Models\Utils\Service_type;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

class Utils implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $expiresAt;
    protected $module; //更新module
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($module)
    {
        $this->expiresAt = Carbon::now()->addDay(7);
        $this->module = $module;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->module){
            case "service_types":
                $this->service_types();
                break;
            case "service_sources":
                $this->service_sources();
                break;
            case "coors":
                $this->coors();
                break;
            case "contract_types":
                $this->contract_types();
                break;
            case "info1":
                $this->info1();
                break;
            case "info2":
                $this->info2();
                break;
            case "info3":
                $this->info3();
                break;
            case "info5":
                $this->info5();
                break;
            case "plans":
                $this->plans();
                break;
            case "pros":
                $this->pros();
                break;
            default:
                //前期因为合作商都没有超过10个, 所以做成全部检索+select, 后期如果超过50个, 做成search组件
                $this->service_types();
                $this->service_sources();
                $this->coors();
                $this->contract_types();
                $this->info1();
                $this->info2();
                $this->info3();
                $this->info5();
                $this->plans();
                $this->pros();
                break;
        }
        $this->job->delete();
    }

    private function service_types(){
        $service_types = Service_type::all()->toArray();  //服务单类型
        Cache::put('service_types', $service_types , $this->expiresAt);
    }

    private function service_sources(){
        $service_sources = Service_source::all()->toArray();  //服务单来源
        Cache::put('service_sources', $service_sources ,  $this->expiresAt);
    }

    private function coors(){
        $coors = Coor::all()->toArray();  //协作商
        Cache::put('coors', $coors , $this->expiresAt);
    }

    private function contract_types(){
        $contract_types = Contract_type::all()->toArray();  //合同类型
        Cache::put('contract_types', $contract_types ,  $this->expiresAt);
    }

    private function info1(){
        $daikuans = Channel_info1::all()->toArray();   //带宽
        Cache::put('daikuans', $daikuans , $this->expiresAt);
    }

    private function info2(){
        $zhantypes =Channel_info2::all()->toArray();    //站类型
        Cache::put('zhantypes', $zhantypes , $this->expiresAt);
    }

    private function info3(){
        $tongxins = Channel_info3::all()->toArray();  //通信卫星
        Cache::put('tongxins', $tongxins , $this->expiresAt);
    }

    private function info5(){
        $jihuas = Channel_info5::all()->toArray();     //极化
        Cache::put('jihuas', $jihuas , $this->expiresAt);
    }

    private function plans(){
        $plans = Plan::all()->toArray();                //套餐
        Cache::put('plans', $plans , $this->expiresAt);
    }

    private function pros(){
        $pros = Profession::all()->toArray();   //行业表
        Cache::put('pros', $pros , $this->expiresAt);
    }
}