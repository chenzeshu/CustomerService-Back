<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\contractc\ContractcRequest;
use App\Http\Traits\UploadTrait;
use App\Models\Contractc;
use App\Models\Money\ChannelMoneyDetail;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//信道合同
class ContractcController extends ApiController
{
    use UploadTrait;

    function __construct()
    {
        $this->save_path = "contractcs";
    }

    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Contractc::orderBy('id', 'desc')->offset($begin)->limit($pageSize)
            ->with([
                'company',
                'ChannelMoney.ChannelMoneyDetails',
                'ChannelMoney.checker',
            ])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = $item->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();

        $total = Contractc::count();
        $data = [
            'data' => $cons,
            'total' => $total,
        ];
        return $this->res(200, '信道合同', $data);
    }

    public function store(ContractcRequest $request)
    {
        //todo  文件上传
        if($request->has('fileList')){
            $ids = $this->moveAndSaveFiles($request->fileList);
            $request['document'] = $ids;
            unset($request['fileList']);
        }

        $data = Contractc::create($request->except('company'));
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

        $re = Contractc::findOrFail($id)->update($request->except('company'));
        return $this->res(2003, '更新信道合同成功', $re);
    }

    /**
     * 更新合同详情
     * @param $contract_id
     */
    public function updateMoney($contractc_id, Request $request)
    {
        Contractc::findOrFail($contractc_id)
            ->ChannelMoney()
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
    public function createMoneyDetail($contractc_id, Request $request)
    {
        Contractc::findOrFail($contractc_id)
            ->ChannelMoney()
            ->first()
            ->ChannelMoneyDetails()
            ->create($request->all());

        return $this->res(2006, '成功');
    }

    /**
     * 历次回款记录的删除
     */
    public function delMoneyDetail($money_detail_id)
    {
        ChannelMoneyDetail::destroy($money_detail_id);

        return $this->res(2006, '成功');
    }

    public function destroy($id)
    {
        $re = Contractc::findOrFail($id)->delete();
        return $this->res(2004, '删除成功', $re);
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

        $total = Contractc::where('name', 'like', '%'.$contract_id.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }
}
