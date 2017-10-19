<?php

namespace App\Http\Controllers\v1\Back;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;

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
     * 添加员工时, 模糊搜索公司
     */
    public function searchCompanies($companyName)
    {
        $companies = Company::where('name', 'like', '%'.$companyName.'%')
                            ->limit(10)
                            ->get()
                            ->toArray();
        $total  = Company::where('name', 'like', '%'.$companyName.'%')
                         ->count();
        $data = [
            'data' => $companies,
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
        $changed_at = null;
        if($request->status == 'online'){
            $changed_at = date('Y-m-d H:i:s', time());
        }

        Employee::findOrFail($request->id)->update([
            'name'=>$request->name,
            'company_id' => $request->company_id,
            'phone'=>$request->phone,
            'email'=>$request->email,
            'status'=>$request->status,
            'openid'=>$request->openid,
            'changed_at'=> $changed_at
        ]);

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
