<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\BaseException;
use App\Exceptions\ScopeExp\ScopeExp;
use App\Exceptions\Services\NeedPositiveNumberException;
use App\Exceptions\Services\TimePassedException;
use App\Exceptions\Services\TooMuchUseException;
use App\Http\Helpers\JWTHelper;
use App\Http\Helpers\Scope;
use App\Http\Requests\Service\ServiceStoreRequest;
use App\Http\Traits\UploadTrait;
use App\Models\Contract;
use App\Models\Services\Contract_plan;
use App\Models\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends ApiController
{
    use UploadTrait;

    function __construct()
    {
        $this->save_path = "services";
    }

    /**
     * 分页
     * @param $page
     * @param $pageSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize, $status="", $charge_flag="")
    {
        $begin = ( $page -1 ) * $pageSize;
        $services = Service::get_pagination($status, $charge_flag, $begin, $pageSize);
        $total = Service::get_total($status, $charge_flag);
        list($service_types, $service_sources) = Service::get_cache();

        $data = [
            'data' => $services,
            'total' => $total,
            'types' => $service_types,
            'sources' => $service_sources
        ];
        return $this->res(200, '服务单信息', $data);
    }

    /**
     * 筛选出待审核的服务单
     */
    public function verify()
    {
        $emp = Service::where('status','=','待审核')
            ->with(['customer', 'contract', 'source', 'type', 'refer_man.company'])
            ->get()
            ->each(function ($ser){
                $ser['project_manager'] = $ser['contract']['PM'] == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->contract->PM})");
            })
            ->toArray();
        $total = Service::where('status','=','待审核')->count();
        $data = [
            'data' => $emp,
            'total' => $total,
        ];
        return $this->res(200, '待审核服务申请', $data);
    }

    /**
     * 筛选出待审核的临时合同的服务单 --- 钱正宇
     */
    public function verifyTemp(Request $request)
    {
        try{
            $user_scope = JWTHelper::getUserScope($request);
            if( $user_scope < Scope::TEMP_CONTRACT_SERVICE_MANAGER ){
                throw new ScopeExp();
            }
            $model = Service::where('status','=','待审核')
                ->with(['customer', 'contract'=>function($query){
                    return $query->where('name', '临时合同');
                }, 'source', 'type', 'refer_man.company'])
                ->get()
                ->reject(function ($item){
                    return $item->contract == null;
                });
            $emp = $model
                ->each(function ($ser){
                    $ser['project_manager'] = $ser['contract']['PM'] == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->contract->PM})");
                })
                ->toArray();
            $total = $model->count();
            $data = [
                'data' => $emp,
                'total' => $total,
            ];
            return $this->res(200, '待审核服务申请', $data);
        }catch (BaseException $e){
            $data = [
                'code' => $e->code,  //-4001
                'message' => $e->msg
            ];
            return $this->res(401, $e->msg, $data);
        }
    }

    /**
     * 通过未审核服务单
     */
    public function pass($id)
    {
        $re = Service::findOrFail($id)->update([
            'status'=>'待派单'
        ]);
        return $this->res(200, '审核通过, 用户将收到通知');
    }

    public function rej($id)
    {
        $re = Service::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);
        return $this->res(200, '已拒绝, 用户将收到通知');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        try{
            //todo 拿到合同的截止日期, 如果过期, 抛出服务过期异常, 相当于也做了时间套餐的校验
            $con = Contract::findOrFail($request->contract_id);
            if($con->time3 < date('y-m-d', time())){
                throw new TimePassedException();
            }
            //todo  通过前端传来的type字段, 知道服务中间表的id, 从而知道服务的total和use以及服务类型planUtil的细节
            $model = Contract_plan::with('planUtil')->findOrFail($request->type);

            //拿到了这个服务单的所有细节
            if($model->toArray()['plan_util']['type2'] == "普通"){ //普通的要比较次数
                if($model->use >= $model->total){
                    throw new TooMuchUseException();
                }
            }
            //fixme 对于预算10000 ,但是超支的 这里就不做校验了
            $plan_num = $request->has('plan_num') ? $request->plan_num : 1;
            if($plan_num < 0){
                throw new NeedPositiveNumberException();
            }
            $total_use = $model->use + $plan_num;
            $model->update(['use'=> $total_use ]);  //套餐使用表的总量修改
        }
        catch (TimePassedException $e){
            return $this->res($e->code, $e->msg);
        }
        catch (TooMuchUseException $e){
            return $this->res($e->code, $e->msg);
        }
        catch (NeedPositiveNumberException $e){
            return $this->res($e->code, $e->msg);
        }

        //todo  临时文件移入永久文件夹
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }

        //todo 检查响应时间
        if($request->status == "待审核" || $request->status == "已派单"){
            $request['time3'] = date('Y-m-d H:i:s', time());
        }

        //todo 存储
        $data = Service::create($request->all());
        return $this->res(2002, "新建信道服务单成功", $data);
    }

    //todo 派单时的方法及触发短信/邮件/内部通知
    public function waitingWork()
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceStoreRequest $request, $id)
    {
        $update = Service::findOrFail($id);

        //修改时, 要校验跟之前的套餐是否相同, 如果不同, 之前的减一, 之后的加一
        //更好的方法是, update就不给修改, 应该删掉重发, 删掉自动触发监听, 对套餐中间表进行修改
        //否则更改服务单的代价太大了

        //todo 文件
        if($request->has('fileList')){
            //todo 检查过滤新旧文件
            $doc_id =  Service::where('id', $id)->first(['document']);
            $request['document'] = $this->getFinalIds($request, $doc_id);
            unset($request['fileList']);
        }

        //fixme 不支持修改合同单号, 所以前端只有灰色, 没有修改可能
        if($request->status == "待审核" && $update->status != "待审核"){  //变成待审核后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }
        if($request->status == "已派单" && $update->status != "已派单"){   //变成已派单后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }

        //todo 修改
        $re = $update->update($request->except(['contract','company', 'visits']));
        if($re){
            return $this->res(2003, "修改服务单成功");
        } else {
            return $this->res(500, "修改服务单失败");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $re = Service::find($id)->delete();
        //fixme 没有删除文件啊



        if($re){
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }

    /**
     * 回访
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visit(Request $request, $id)
    {
        $re = Service::findOrFail($id)->visits()->updateOrCreate($request->except(['employees', 'status','id']));
        if($re){
            return $this->res(2005, "填写回访成功");
        } else {
            return $this->res(500, "填写回访失败");
        }
    }

}
