<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Helpers\Params;
use App\Http\Repositories\ContractcRepo;
use App\Http\Requests\contractc\ContractcRequest;
use App\Http\Traits\UploadTrait;
use App\Id_record;
use App\Jobs\Cache\RefreshContractcs;
use App\Models\Channels\Contractc_plan;
use App\Models\Contractc;
use App\Models\Money\ChannelMoneyDetail;
use App\Observers\OBTraits\ContractcUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

//信道合同
class ContractcController extends ApiController
{
    use UploadTrait;
    protected $repo;
    function __construct(ContractcRepo $repo)
    {
        $this->save_path = "contractcs";
        $this->repo = $repo;
    }

    public function page($page, $pageSize, Request $request)
    {
        $status = $request->value1;
        $other = $request->value2;
        $cons = Cache::get('contractcs');
        if( empty($cons) ){
            Contractc::redis_refresh_data();
            $cons = Cache::get('contractcs');
        }
        list($cons,$total) = $this->repo->pageFilter($cons, $finish, $page, $pageSize);
        $contractc_plans = Cache::get('channel_plans');
        $data = [
            'data' => $cons,
            'total' => $total,
            'contractc_plans' =>$contractc_plans
        ];
        return $this->res(200, '信道合同', $data);
    }

    public function store(ContractcRequest $request)
    {
        //todo 合同编号
        $recordModel = Id_record::find(3);
        $record = $recordModel->record;
        $len = 3 - strlen($record);
        $request['contract_id'] ="中网信字".date('Y', time()).zerofill($len).$record;

        //todo  文件上传
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }
        $data = Contractc::create($request->except('company'));
        $recordModel->increment('record');
        return $this->res(2002, '新建信道合同成功', $data);
    }

    public function update(ContractcRequest $request, $id)
    {
        //todo 文件
        if($request->has('fileList')){
            //todo 检查过滤新旧文件
            $doc_id =  Contractc::where('id', $id)->first(['document']);
            $request['document'] = $this->getFinalIds($request, $doc_id);
            unset($request['fileList']);
        }
        $re = Contractc::findOrFail($id)->update($request->except(['company', 'channel_money', 'contractc_plans']));
        return $this->res(2003, '更新信道合同成功', $re);
    }

    /**
     * 删除信道合同
     * @param $id
     */
    public function destroy($id)
    {
        $model = Contractc::findOrFail($id);
        //todo 删除文件
        $this->deleteFilesForDestroy($model->document);
        //todo 删除回款总览&回款详情
        $model->ChannelMoney()
            ->each(function ($c){
                $c->ChannelMoneyDetails()->get()->each(function ($m){
                    $m->delete();
                });
                $c->delete();
            });
        $model->delete();
        return $this->res(2004, '删除成功');
    }

    /**
     * 更新合同回款详情
     * @param $contract_id
     */
    public function updateMoney($contractc_id, Request $request)
    {
        Contractc::findOrFail($contractc_id)
            ->ChannelMoney()
            ->update($request->except([
                'channel_money_details',
                'checker',
                'contract_id',
                'reach',
                'left'
            ]));
        Contractc::forget_cache();
        return $this->res(2006, '成功');
    }

    /**
     * 新建历次回款记录
     */
    public function createMoneyDetail($contractc_id, Request $request)
    {
        Contractc::findOrFail($contractc_id)
            ->ChannelMoney()
            ->first()
            ->ChannelMoneyDetails()
            ->create($request->all());
        Contractc::forget_cache();
        return $this->res(2006, '成功');
    }

    /**
     * 历次回款记录的删除
     */
    public function delMoneyDetail($money_detail_id)
    {
        ChannelMoneyDetail::destroy($money_detail_id);
        Contractc::forget_cache();
        return $this->res(2006, '成功');
    }

    //要求关键字模糊查询
    public function search($contract_id,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Contractc::where('contract_id', 'like', '%'.$contract_id.'%')
            ->orderBy('id', 'desc')
            ->offset($begin)
            ->limit($pageSize)
            ->with('company')
            ->get()
            ->toArray();

        $total = Contractc::where('contract_id', 'like', '%'.$contract_id.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 得到合同名下信道套餐
     */
    public function getContractcPlans($contractc_id)
    {
        $data = Contractc::findOrFail($contractc_id)->contractc_plans()->get();
        return $this->res(200, '套餐列表', $data);
    }

    /**
     * 增加套餐
     * @param $contractc_id
     * @request->total 单位为分钟
     */
    public function addPlan($contractc_id, Request $request)
    {
        $total =  floor($request->total / Params::ChannelTotalUnit);
        $re = Contractc_plan::create([
           'contractc_id' => $contractc_id,
           'plan_id' => $request->plan_id,
           'total' => $total, //套餐总量,
           'alias' => $request->alias
        ]);

        if($re){
            return $this->res(2004, '创建套餐成功');
        }
    }

    /**
     * 删除套餐
     * @param $contractc_id
     */
    public function deletePlan($contractc_id)
    {
        $re = Contractc_plan::destroy($contractc_id);
        if($re){
            return $this->res(2006, '删除套餐成功');
        }
    }
}
