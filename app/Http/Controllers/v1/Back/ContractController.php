<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\contract\ContractStoreRequest;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Utils\Contract_type;
use App\Models\Utils\Coor;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    use ReturnTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->res(200, 'contract');
    }

    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Contract::orderBy('id', 'desc')->offset($begin)->limit($pageSize)
            ->with('company')
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->PM = DB::select("select `id`, `name` from employees where id in ({$item->PM})");
                $item->TM = DB::select("select `id`, `name` from employees where id in ({$item->TM})");
                $item->document = DB::select("select * from docs where id in ({$item->document})");
                return $item;
            })
            ->toArray();
        
        //前期因为合作商都没有超过10个, 所以直接当成utils了, 后期应该做成查询
        $coors = Coor::all()->toArray();
        $types = Contract_type::all()->toArray();
        $total = Contract::count();
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
        //如果从company入口进入, 前端记录并并入了company_id
        $data = Contract::create($request->all());

        return $this->res(200, "新建合同成功", ['data'=>$data]);
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
        //fixme 修改时前端默认company_id的单位是灰色的, 除非选择更改公司按钮, 否则无法更改
        $re = Contract::find($id)->update($request->all());
        if($re){
            return $this->res(200, "修改合同成功");
        } else {
            return $this->res(500, "修改合同失败");
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
        $re = Contract::find($id)->delete();
        if($re){
            return $this->res(200, "删除合同成功");
        } else {
            return $this->res(500, "删除合同失败");
        }
    }
}
