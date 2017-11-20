<?php

namespace App\Http\Controllers\v1\Back;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Contractc;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * 分页展示
     */
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Employee::orderBy('id', 'desc')->offset($begin)->limit($pageSize)->with('company')->get()->toArray();
        $total = Employee::count();
        $data = [
                    'data' => $emp,
                    'total' => $total,
                ];
        return $this->res(200, '员工信息', $data);
    }

    /**
     * 筛选出待审核的用户
     */
    public function verify()
    {
        $emp = Employee::where('status','=','offline')->with('company')->get()->toArray();
        $total = Employee::where('status','=','offline')->count();
        $data = [
            'data' => $emp,
            'total' => $total,
        ];
        return $this->res(200, '待审核用户', $data);
    }

    /**
     * 通过未审核者(将offline或"离职"者直接转变成"online")
     */
    public function pass($id)
    {
        $re = Employee::findOrFail($id)->update([
           'status'=>'online'
        ]);

        //todo 应该同时发送微信小程序消息+一条短信给被通过的用户
        //...
        return $this->res(200, '审核通过, 用户将收到通知');
    }

    /**
     * 拒绝未审核者(将offline或"离职"者直接转变成"online")
     */
    public function rej($id)
    {
        $re = Employee::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);

        //todo 应该同时发送微信小程序消息+一条短信给被拒绝的用户
        //fixme    万一是恶意注册???
        //...
        return $this->res(200, '已拒绝, 用户将收到通知');
    }

    /**
     * 要求关键字模糊查询
     */
    public function search($name, $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Employee::where('name', 'like', '%'.$name.'%')
                        ->orderBy('id', 'desc')
                        ->offset($begin)
                        ->limit($pageSize)
                        ->with('company')
                        ->get()
                        ->toArray();

        $total = Employee::where('name', 'like', '%'.$name.'%')
                        ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 模糊搜索公司
     */
    public function searchCompanies($companyName)
    {
        $companies = Company::where('name', 'like', '%'.$companyName.'%')
                            ->limit(10)
                            ->get(['id', 'name'])
                            ->toArray();
        $total  = Company::where('name', 'like', '%'.$companyName.'%')
                         ->count();
        $data = [
            'data' => $companies,
            'sTotal' => $total
        ];
        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 模糊搜索外部员工
     */
    public function searchOutEmps($empName)
    {
        $emps = DB::table('employees')
                    ->where('company_id',"!=",1)
                    ->where('name','like', "%".$empName."%")
                    ->limit(10)
                    ->get(['id', 'name']);
        $total = Employee::where('name', 'like', '%'.$empName.'%')
                          ->count();

        $data = [
            'data' => $emps,
            'sTotal' => $total
        ];
        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 模糊搜索内部员工
     */
    public function searchInnerEmps($empName)
    {
        $emps = DB::table('employees')
            ->where('company_id', 1)
            ->where('name','like', "%".$empName."%")
            ->limit(10)
            ->get(['id', 'name']);
        $total = Employee::where('name', 'like', '%'.$empName.'%')
            ->count();

        $data = [
            'data' => $emps,
            'sTotal' => $total
        ];
        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 搜索普通合同
     */
    public function searchContracts($contract_id)
    {
        $contracts = DB::table('contracts')
            ->where('contract_id','like', "%".$contract_id."%")
            ->limit(10)
            ->get(['id', 'contract_id']);
        $total = Contract::where('contract_id', 'like', '%'.$contract_id.'%')
            ->count();

        $data = [
            'data' => $contracts,
            'sTotal' => $total
        ];
        return $this->res(200, '搜索结果', $data);
    }

    /**
     * 搜索信道合同
     */
    public function searchContractcs($contract_id)
    {
        $contracts = DB::table('contractcs')
            ->where('contract_id','like', "%".$contract_id."%")
            ->limit(10)
            ->get(['id', 'contract_id']);
        $total = Contractc::where('contract_id', 'like', '%'.$contract_id.'%')
            ->count();

        $data = [
            'data' => $contracts,
            'sTotal' => $total
        ];
        return $this->res(200, '搜索结果', $data);
    }

    public function store(Request $request)
    {
        $company_id = $request->company_id;
        $changed_at = null;
        if($request->status == 'online'){
            $changed_at = date('Y-m-d H:i:s', time());
        }

        $data = Company::findOrFail($company_id)->employees()->create([
            'name'=>$request->name,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'status'=>$request->status,
            'openid'=>$request->openid,
            'changed_at'=> $changed_at
        ]);

        $company = Company::findOrFail($company_id);
        $data['company'] = $company;

        return $this->res('2002', '添加成功', $data);
    }

    /**
     * 将人员改为下线状态: 此状态下将无法登陆(小程序端显示被冻结, 请联系管理员)
     */
    public function offline($id)
    {
        Employee::findOrFail($id)->update([
           'status'=>'offline',
        ]);
        return $this->res('2005', '下线成功');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->status == 'online'){
            $request->change_at = date('Y-m-d H:i:s', time());
        }

        Employee::findOrFail($request->id)->update($request->except('company'));

        return $this->res('2003', '修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::destroy($id);

        return $this->res(2004, '删除成功');
    }
}
