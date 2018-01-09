<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ContractRepo;
use App\Http\Requests\contract\ContractStoreRequest;
use App\Http\Traits\UploadTrait;
use App\Id_record;
use App\Jobs\Cache\RefreshContracts;
use App\Models\Contract;
use App\Models\Money\ServiceMoneyDetail;
use App\Models\Services\Contract_plan;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    use ReturnTrait, UploadTrait;

    protected $repo;
    function __construct(ContractRepo $repo)
    {
        $this->save_path = "contracts";
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->res(200, 'contract');
    }

    /**
     * 缓存化的分页获取模式
     * @param $page
     * @param $pageSize
     * @param string $finish finish字段 是否结清
     * @param string $other 预留字段
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize, $finish="", $other="")
    {
        $cons = Cache::get('contracts');

        if( empty($cons) ){
            Contract::redis_refresh_data();
            $cons = Cache::get('contracts');
        }
        list($cons,$total) = $this->repo->pageFilter($cons, $finish, $page, $pageSize);
        $cache = Contract::get_cache();

        $data = [
            'data' => $cons,
            'total' => $total,
        ];
        $data = array_merge($data, $cache);
        return $this->res(200, '普合信息', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractStoreRequest $request)
    {
        //contract_id规则写在前端
        if($request->type2 == "销售"){
            $recordModel = Id_record::find(1);
            $record = $recordModel->record;
            $len = 3 - strlen($record);
            $request['contract_id'] ="中网销字".date('Y', time()).zerofill($len).$record;
        }else{
            $recordModel = Id_record::find(2);
            $record = $recordModel->record;
            $len = 3 - strlen($record);
            $request['contract_id'] = "中网客字".date('Y', time()).zerofill($len).$record;
        }

        //如果从company入口进入, 前端记录并并入了company_id
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }

        $data = Contract::create($request->except('company'));
        $recordModel->increment('record');
        return $this->res(2002, "新建合同成功", ['data'=>$data]);
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
    public function update(ContractStoreRequest $request, $id)
    {
        //todo 文件
        if($request->has('fileList')){
            //todo 检查过滤新旧文件
            $doc_id =  Contract::where('id', $id)->first(['document']);
            $request['document'] = $this->getFinalIds($request, $doc_id);
            unset($request['fileList']);
        }
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Contract::findOrFail($id)->update($request->except(['company','service_money','contract_plans', 'documents']));
        return $re ? $this->res(2003, "修改合同成功") : $this->res(-2003, "修改合同失败");
    }

    /**
     * 删除合同
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $con = Contract::findOrFail($id);
        $this->deleteFilesForDestroy($con->document);  //删除文件及mysql记录
        //todo 删除本合同的(1)回款表总览信息, (2)回款细节, (3)关联套餐信息
        DB::delete("DELETE s1, s2, c3 FROM contracts as c
          INNER JOIN service_moneys as s1 ON s1.contract_id = c.id
          INNER JOIN service_money_details as s2 ON s1.id = s2.service_money_id
          INNER JOIN contract_plans as c3 ON c.id = c3.contract_id
          WHERE c.id = '$id'");
        $re = $con->delete();  //不并入sql是为了触发模型事件
        if($re || $re == 0){
            return $this->res(2004, "删除合同成功");
        } else {
            return $this->res(500, "删除合同失败");
        }
    }

    //todo 要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Contract::where('name', 'like', '%'.$name.'%')
            ->orderBy('id', 'desc')
            ->offset($begin)
            ->limit($pageSize)
            ->with('company')
            ->get()
            ->toArray();

        $total = Contract::where('name', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 更新合同详情
     * @param $contract_id
     */
    public function updateMoney($contract_id, Request $request)
    {
        Contract::findOrFail($contract_id)
            ->ServiceMoney()
            ->update($request->except([
                'checker',
                'contract_id',
                'reach',
                'service_money_details',
                'left'
            ]));
        return $this->res(2006, '成功');
    }

    /**
     * 新建历次回款记录
     */
    public function createMoneyDetail($contract_id, Request $request)
    {
        Contract::findOrFail($contract_id)
            ->ServiceMoney()
            ->first()
            ->ServiceMoneyDetails()
            ->create($request->all());
        return $this->res(2006, '成功');
    }

    /**
     * 历次回款记录的删除
     */
    public function delMoneyDetail($money_detail_id)
    {
        ServiceMoneyDetail::destroy($money_detail_id);
        return $this->res(2006, '成功');
    }

    //todo 检索合同下的套餐( 因为一个合同可能有多个同类型套餐, 所以服务单展示的是中间表的desc,)
    public function getContractPlans($contract_id)
    {
        $data = Contract::with('Contract_plans.planUtil')->findOrFail($contract_id)->toArray()['contract_plans'];
        return $this->res(200, '套餐信息', $data);
    }

    //todo 为合同新增一个套餐
    public function addPlan($contract_id, Request $request)
    {
        Contract::findOrFail($contract_id)->Contract_plans()->create($request->all());
        return $this->res(2004, '新增成功');
    }

    //todo 删除合同的一个套餐
    public function deletePlan($id)
    {
        Contract_plan::destroy($id);
        //fixme 隐患: 删了一个套餐后, 合同使用这个套餐的的服务单如何处理?
        //fixme  这里只能硬性要求了: 合同录入者必须严谨
        //fixme  或者给套餐一个新的标签:废弃(类似软删除)(和使用完/过期不通, 是废除)
        return $this->res(2006, '删除成功');
    }
}
