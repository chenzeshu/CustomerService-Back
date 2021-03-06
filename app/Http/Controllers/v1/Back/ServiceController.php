<?php

namespace App\Http\Controllers\v1\Back;

use App\Exceptions\BaseException;
use App\Exceptions\ScopeExp\ScopeExp;
use App\Exceptions\Services\NeedPositiveNumberException;
use App\Exceptions\Services\TimePassedException;
use App\Exceptions\Services\TooMuchUseException;
use App\Http\Helpers\JWTHelper;
use App\Http\Helpers\Scope;
use App\Http\Repositories\ServiceRepo;
use App\Http\Requests\Service\ServiceStoreRequest;
use App\Http\Resources\Back\ServiceVerifyCollection;
use App\Http\Traits\UploadTrait;
use App\Id_record;
use App\Models\Contract;
use App\Models\Problem\ProblemType;
use App\Models\Services\Contract_plan;
use App\Models\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends ApiController
{
    use UploadTrait;
    protected $repo;

    function __construct(ServiceRepo $repo)
    {
        $this->save_path = "services";
        $this->repo = $repo;
    }

    /**
     * 分页
     * @param $page
     * @param $pageSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize, Request $request)
    {
        $status = $request->value1;
        $charge_flag = $request->value2;
        $company_name = $request->value3;
        $emp_name = $request->value4;
        $service_id = $request->value5;

        $begin = ( $page -1 ) * $pageSize;
        $services = Service::get_pagination($status, $charge_flag, $begin, $pageSize);
        $services = $this->repo->filterData($services, $company_name, $emp_name, $service_id);
        $total = Service::get_total($status, $charge_flag);
        //假如是搜索筛选
        if(trim($company_name)|| trim($emp_name)){
            $total = count($services);
        }

        list($service_types, $service_sources) = Service::get_cache();
        $problem_types = ProblemType::get(['ptype_name', 'ptype_id'])->toArray();
        $data = [
            'data' => $services,
            'total' => $total,
            'types' => $service_types,
            'sources' => $service_sources,
            'problem_types' => $problem_types
        ];
        return $this->res(200, '服务单信息', $data);
    }

    /**
     * 展示不同状态的服务单（默认为待审核）
     */
    public function verify($status = "待审核")
    {
        $emp = Service::where('status','=',$status)
            ->with(['customer', 'contract', 'source', 'type', 'refer_man.company'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->each(function ($ser){
                $ser['project_manager'] = $ser['contract']['PM'] == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->contract->PM})");
                $ser['workman'] = $ser->man == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->man})");
                $ser['doc'] =  $ser->document == null ? null : DB::select("select `path` from docs where id in ({$ser->document}) and name = '申请证据'");
            });
        $emp = new ServiceVerifyCollection($emp);

        $total = Service::where('status','=',$status)->count();
        $data = [
            'data' => $emp,
            'total' => $total
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
                ->orderBy('updated_at', 'desc')
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
            return $this->error($e);
        }
    }

    /**
     * post方法
     * 为其选择套餐并通过未审核服务单
     */
    public function pass(Request $request)
    {
        try{
            $update = Service::findOrFail($request->service_id);
            //fixme 此处为小程序提交与审核，此时默认num为1， 也确实没有什么影响，具体的操作在服务单页面改了。
            $this->repo->myIncrement($update, 'use', config("app.plan.service.default_num"), $request->plan_id);

            $update->update([
                'status'=>'待派单',
                'contract_plan_id' => $request->plan_id,
            ]);
            return $this->res(200, '审核通过, 用户将收到通知');
        } catch(TooMuchUseException $e){
            return $this->res($e->code, $e->msg);
        }
    }

    /**
     * 拒绝申请
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
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
            //todo  前端的contract_plan_id字段, 即contract_plan表的id, 可知服务的total、use以及服务类型planUtil的细节
            $model = Contract_plan::with('planUtil')->findOrFail($request->contract_plan_id);

            //拿到了这个服务单的所有细节
            if($model->toArray()['plan_util']['type2'] == "普通"){ //普通的要比较次数
                if($model->use >= $model->total){
                    throw new TooMuchUseException();
                }
            }
            //todo 做对套餐的影响
                //fixme 对于预算10000 ,但是超支的 这里就不做校验了
                $plan_num = $request->has('plan_num') ? $request->plan_num : 1;
                //todo 去除恶意负数
                if($plan_num < 0){
                    throw new NeedPositiveNumberException();
                }
                //todo 审核是否拒绝, 如果直接建立了一个拒绝的单子, 那么不需要加一
                if($request->status == "拒绝"){
                    $plan_num = 0;
                }
                $model->update(['use'=> $plan_num]);  //使用量增加

            //todo 自动生成服务单编号
            $recordModel = Id_record::find(4);  //模型的自加放在服务单生成成功时
            $record = $recordModel->record;
            $len = 3 - strlen($record);
            $request['service_id'] = config('app.regex.service').date('Y', time()).zerofill($len).$record;

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
            $data = Service::create($request->except(['problem_type', 'problem_step', 'problem_desc',
                'problem_solution', 'problem_urgency', 'problem_importance', 'device_ids']));
            if($data){
                //存储成功后，得到新服务单id
                if($request->problem_if == 1){
                    $request->problem_if = true;
                    $problem_data = [
                        "service_id" => $data->id,
                        "problem_type" => $request->problem_type,  //故障类型id
                        "problem_step" => $request->problem_step,  //故障类型id
                        "problem_desc" => $request->desc1,
                        "problem_solution" => $request->desc2,
                        "problem_urgency" =>$request->problem_urgency,
                        "problem_importance" => $request->problem_importance,
                    ];
                    $this->repo->synchronize_problem($problem_data, $request->device_ids);
                }

                //todo 服务单生成成功, 此时可以放心编号record加1
                $recordModel->increment('record');
                return $this->res(2002, "新建信道服务单成功", $data);
            }
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
        //todo 注意： 因为用户通过小程序提交时，不会绝对清楚应该提交什么服务类型，所以不能强制后台只能选择合同下对应套餐，而是展示所有套餐。
        //todo 同时，在update时，由于必须选择套餐，所以套餐应被更新到services的contract_plan_id里
        $update = Service::findOrFail($id);
        //修改时, 要校验跟之前的套餐是否相同, 如果不同, 之前的减一, 之后的加一
        //更好的方法是, update就不给修改套餐, 应该删掉重发, 删掉自动触发监听, 对套餐中间表进行修改
        //否则更改服务单的代价太大了, 无论是待审核->待派单, 还是什么都会有影响, 太细了, 不适合这个版本

        //todo 审核是否拒绝
        //todo 如果由【非拒绝或非待审批状态】变为拒绝, 减1;
        if($request->status == "拒绝" && $update->status != '待审批' && $update->status !="拒绝"){
            //todo 不使用increment是为了更精确地触发模型事件(而不是一更新服务单就去触发, 粒度细化为plan)
            $this->repo->myDecrement($update, 'use', $request->plan_num);
        }else if ($request->status != "拒绝" && $request->status != '待审批' && $update->status == "拒绝"){
            //todo 拒绝变为非待审批状态, 加1;
            $this->repo->myIncrement($update, 'use', $request->plan_num);
        }

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
        $re = $update->update($request->except(['contract','company', 'visits', 'problem', 'device_ids']));
        if($re){
            //存储成功后，得到新服务单id
            if($request->problem_if == 1){
                $request->problem_if = true;
                $problem = $request->problem;
                $problem_data = [
                    "problem_id" => $problem['problem_id'],
                    "service_id" => $request->id,
                    "problem_type" => $problem['problem_type'],  //故障类型id
                    "problem_step" => $problem['problem_step'],  //故障类型id
                    "problem_desc" => $request->desc1,
                    "problem_solution" => $request->desc2,
                    "problem_urgency" => $problem['problem_urgency'],
                    "problem_importance" => $problem['problem_importance'],
                ];
                $this->repo->update_problem($problem_data, $request->device_ids);
            }

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
        $model = Service::find($id);
        //todo 1. 删除文件
        $this->deleteFilesForDestroy($model->document);  //删除文件及mysql记录
        //todo 2. 删除对套餐记录的"贡献"
        if($model->status != '拒绝'){     //todo 如果是"拒绝'的状态, 就不减
            $this->repo->myDecrement($model, 'use', $model->plan_num);
        }
        //todo 3. 删除服务单本身
        $re = $model->delete();
        if($re){
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
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
     * 回访
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visit(Request $request, $id)
    {
        $model = Service::findOrFail($id)->visits();
        if($model->where('service_id', $id)->first()){
            $re = $model->where('service_id', $id)->update($request->except(['employees', 'status','id']));
        }else{
            $re = $model->create($request->except(['employees', 'status','id']));
        }
        if($re){
            return $this->res(2005, "填写回访成功");
        } else {
            return $this->res(500, "填写回访失败");
        }
    }

    /**
     * 等小程序的API可以在PC上调试时再传图了
     * 管理员同意员工的服务单  申请完成 => 已完成
     * @int $service_id 服务单id
     */
    public function passFinish($service_id, Request $request)
    {
        $service = Service::findOrFail($service_id);
        $admin = JWTHelper::getUser($request);
        //todo 通知员工通过
        $man = explode(",", $service->man);
        foreach ($man as $m){
            //todo 队列通知
        }
        //todo 通知用户正式确认已完成
        $cus = $service->customer;
            //todo 队列通知

        $service->update(['status' => '已完成']);
        Log::info('管理员['.$admin->name.']通过了服务单id'. $service_id .'的完成申请');
        return $this->res(7006, '通过申请');
    }

    /**
     * 管理员拒绝员工的服务单  申请完成 => 不变
     * @int $service_id 服务单id
     */
    public function rejectFinish($service_id, Request $request)
    {
        $admin = JWTHelper::getUser($request);
        Log::info('管理员['.$admin->name.']拒绝了服务单id'. $service_id .'的完成申请');
        //todo 通知员工被拒绝
        $service = Service::findOrFail($service_id);
        $man = explode(",", $service->man);
        foreach ($man as $m){
            //todo 队列通知
        }

        return $this->res(-7006, '拒绝申请');
    }
}
