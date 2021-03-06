<?php

namespace App\Http\Controllers\v1\back\SP;

use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Controllers\v1\Back\ContractController;
use App\Http\Helpers\Params;
use App\Http\Resources\SP\ServiceProcessCollection;
use App\Id_record;
use App\Models\Contract;
use App\Models\Services\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class repairController extends ApiController
{
    protected $contractController;
    public function __construct(ContractController $contractController)
    {
        $this->contractController = $contractController;
    }

    /**
     * 在选择的合同下创建服务单
     * @param $contract_id
     * @param Request $request
     */
    public function apply(Request $request)
    {
        if($request->contract_id == 'temp'){
            $recordModel = Id_record::find(1);

            //1. 先创建临时合同
            $contract = Contract::create([
                'company_id' => $request->com_id,
                'name' => '临时合同',
                'contract_id' => $this->contractController->generateContractId($recordModel, '销'),
                'type1' => 3,
                'type2' => '销售',
                'PM' => Params::getQianNo(),
            ]);
            //2. 创建非正式的$request对象
                $request->contract_id = $contract->id;
                $request->meal_id = Params::NOMEAL;        //contract_plan.id
//                $request->type_id = $request->type_id;
                $request->customer = $request->cus_id;
//                $request->zhongId = $request->zhongId;
//                $request->question = $request->question;
                $request->source=4;
        }

        //合同过期过滤已在 CommonController制作
        if($request->has('zhongId')){
            //中网员工报修
            $re = ServiceDAO::empCreate($request);
        }else{
            //客户报修
            $re = ServiceDAO::cusCreate($request);
        }
        if($re){
            //todo 向管理员发送一条短信
        }
        return $this->res(7004, '报修成功');
    }

    /**
     * @param $page 当前页数
     * @param $pageSize 每页数据量
     * @param $emp_id 员工id
     * @param $status 服务单状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProcess($page, $pageSize, $emp_id, $status)
    {
        $begin = ($page - 1) * $pageSize;
        $data = Service::with(['type', 'customer', 'refer_man', 'contract.company'])
            ->where('refer_man', $emp_id)
            ->offset($begin)
            ->limit($pageSize)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->filter(function($item) use ($status){
                if($status == "全部"){
                    return true;
                }else{
                    return $item->status == $status ? true : false;
                }
            });
        $status = ServiceDAO::getServiceStatus();

        if( count($data) == 0){
            return $this->res(-7003, '暂无数据',[
                'data' => [],
                'status' => $status
            ]);
        }else{
            try{
                return $this->res(7003, '报修进展列表', [
                    'data' => new ServiceProcessCollection($data),
                    'status' => $status
                ]);
            }catch (ModelNotFoundException $e){
                //ServiceProcessCollection里的ServiceShowResourceForError的Service_type模型会找不到(当前最大id), 报404, 可以捕捉
                //2018.7.31 出现了一直报-7004， 原因是删除了人员表里的20号人物，而项目经理刚好是这个人。
                return $this->res(-7004, 'modelFindBUG', [
                    'data' => [],
                    'status' => $status
                ]);
            }

        }
    }

    /**
     * 用户申述  ---  建议处理手段为: 管理员在后台点击"受理" --> 发送短信给 客服人员 +  项目经理说明情况 -->  发送给用户:"已受理请等待" | 或者将单子"拒绝", 然后重开一单
     */
    public function allege($service_id, Request $request)
    {
       $service = Service::findOrFail($service_id);
       if($service->status == "申述中"){
           //防止提交后还喜欢连按或利用接口漏洞
           return $this->res(-7008, '请勿重复提交');
        }
       $service->update([
            'status' => '申述中',
            'allege' => $request->allege
        ]);
        //todo 发送短信给管理员
        return $this->res(7008, '申述成功');
    }
}
