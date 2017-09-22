<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\company\CompanyStoreRequest;
use App\Models\Company;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;

class CompanyController extends Controller
{
    use ReturnTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Company::all()->toArray();
        return $this->res('2000', '公司信息',  ['data'=>$data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyStoreRequest $request)
    {
        $data = Company::create($request->all());
        return $this->res('2002', '添加成功', ['data'=>$data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //todo 前端提供员工入口
        //todo 前端提供合同入口
        //todo 前端提供服务入口
    }

    /**
     * @param $id 单位id
     * 展示旗下员工
     */
    public function showEmps($id)
    {
        $data = Company::findOrFail($id)->employees()->get();
        return $this->res(2005, '员工信息', ['data' => $data]);
    }

    /**
     * @param $id 单位id
     * 展示旗下合同
     */
    public function showContracts($id)
    {
        $data = Company::findOrFail($id)->contracts()->get();
        return $this->res(2006, '合同信息', ['data'=> $data]);
    }

    /**
     * @param $id 单位id
     * 展示旗下普通服务
     */
    public function showServices($id)
    {
        $data = Company::findOrFail($id)->services()->get();
        return $this->res(2007, '普通服务单信息', ['data'=> $data]);
    }

    /**
     * @param $id 单位id
     * 展示旗下信道服务
     */
    public function showChannels($id)
    {
        $data = Company::findOrFail($id)->channels()->get();
        return $this->res(2008, '信道服务单信息', ['data'=> $data]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyStoreRequest $request, $id)
    {
        /** $re boolean */
        $re = Company::find($id)->update($request->all());
        return true == $re ? $this->res('2003', '修改成功') : $this->res('-2003', '修改失败');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $re = Company::find($id)->delete();
        return true == $re ? $this->res('2004', '删除成功') : $this->res('-2004', '删除失败');
    }

    //要求关键字模糊查询
    public function search($name)
    {
        
    }
}
