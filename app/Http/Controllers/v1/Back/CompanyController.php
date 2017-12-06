<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Requests\company\CompanyStoreRequest;
use App\Models\Company;
use App\Models\Utils\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanyController extends ApiController
{
    protected $pros;    //行业表信息
    protected $pageSize;  //每页数量
    protected $page;    //起始页面
    protected $begin;   //起始位置
    protected $total; //数组总数, 页数的计算交给前端
    protected $data;  //分页详细内容
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Company::all()->toArray();
        return $this->res('2000', '公司信息', $data);
    }

    private function init($page, $pageSize){
        $this->begin = ($page -1 ) * $pageSize;
        $this->pros = Cache::get('pros');
    }

    public function page($page, $pageSize)
    {
        $this->init($page, $pageSize);
        $companies = Company::orderBy('id', 'desc')->offset($this->begin)->limit($pageSize)->get()->toArray();
        $total =  Company::count();
        $data = [
            'data'=> $companies,
            'pros' => $this->pros,
            'total' => $total
        ];
        return $this->res('2000', 200, $data);
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
        return $this->res('2002', '添加成功', $data);
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
        //todo 前端提供已有套餐入口
    }

    /**
     * @param $id 单位id
     * 展示旗下员工
     */
    public function showEmps($id)
    {
        $data = Company::findOrFail($id)->employees()->get();
        return $this->res(2005, '员工信息', $data);
    }

    /**
     * @param $id 单位id
     * 展示旗下合同
     */
    public function showContracts($id)
    {
        $data = Company::findOrFail($id)->contracts()->get();
        return $this->res(2006, '合同信息', $data);
    }

    /**
     * @param $id 单位id
     * 展示旗下普通服务
     */
    public function showServices($id)
    {
        $data = Company::findOrFail($id)->services()->get();
        return $this->res(2007, '普通服务单信息', $data);
    }

    /**
     * @param $id 单位id
     * 展示旗下信道服务
     */
    public function showChannels($id)
    {
        $data = Company::findOrFail($id)->channels()->get();
        return $this->res(2008, '信道服务单信息', $data);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function update(CompanyStoreRequest $request, $id)
    public function update(Request $request, $id)
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
        $re = Company::destroy($id);
        return true == $re ? $this->res('2004', '删除成功') : $this->res('-2004', '删除失败');
    }

    //要求关键字模糊查询
    public function search($name, $page, $pageSize)
    {
        $this->init($page, $pageSize);
        $data = Company::where('name', 'like', '%'.$name.'%')
                        ->orderBy('id', 'desc')
                        ->offset($this->begin)
                        ->limit($pageSize)
                        ->get()
                        ->toArray();

        $total = Company::where('name', 'like', '%'.$name.'%')
                        ->count();

        $data= [
            'data'=> $data,
            'total'=> $total,
        ];
        return $this->res(200, '搜索结果', $data);
    }
}
