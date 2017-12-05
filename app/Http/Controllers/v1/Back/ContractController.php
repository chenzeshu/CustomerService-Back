<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\contract\ContractStoreRequest;
use App\Http\Traits\UploadTrait;
use App\Models\Contract;
use App\Models\Money\ServiceMoneyDetail;
use App\Models\Utils\Contract_type;
use App\Models\Utils\Coor;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    use ReturnTrait, UploadTrait;

    function __construct()
    {
        $this->save_path = "contracts";
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
     * @param $page
     * @param $pageSize
     * @param string $finish finish字段 是否结清
     * @param string $other 预留字段
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize, $finish="", $other="")
    {
        $begin = ( $page -1 ) * $pageSize;
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
        $cons = $consModel
            ->splice($begin, $pageSize)
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->TM = $item->TM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->TM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();

        $total =  $consModel->count();
        //前期因为合作商都没有超过10个, 所以直接当成utils了, 后期应该做成查询
        $coors = Coor::all()->toArray();
        $types = Contract_type::all()->toArray();
        $data = [
            'data' => $cons,
            'total' => $total,
            'coors' => $coors,
            'types' => $types,
        ];
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

        //如果从company入口进入, 前端记录并并入了company_id
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }

        $data = Contract::create($request->except('company'));
        //todo 再造一个空money记录
        $data->ServiceMoney()->create([]);
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
        $re = Contract::find($id)->update($request->except(['company']));

        return $re ? $this->res(2003, "修改合同成功") : $this->res(-2003, "修改合同失败");
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $con = Contract::findOrFail($id);
        $this->deleteFilesForDestroy($con->document);
        $re = $con->delete();
        if($re){
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


}
