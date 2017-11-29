<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\ServiceStoreRequest;
use App\Models\Company;
use App\Models\Services\Service;
use App\Models\Services\Visit;
use App\Models\Utils\Service_source;
use App\Models\Utils\Service_type;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->getPaginator(3, 20);
        return $this->res(200, 'Service', $data);
    }

    /**
     * 分页
     * @param $page
     * @param $pageSize
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $services = Service::where('status', "!=", '待审核')
            ->orderBy('updated_at', 'desc')
            ->offset($begin)->limit($pageSize)
            ->with(['contract','visits.employees','refer_man'])
            ->get()
            ->map(function ($item){
                //todo 拿到人员, 文件(由于是多选, 所以二者只能单独写)
                $item->man = $item->man == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->man})");
                $item->customer = $item->customer == null ? null : DB::select("select `id`, `name`, `phone` from employees where id in ({$item->customer})");
                $item->document = $item->document == null ? null : DB::select("select * from docs where id in ({$item->document})");
                $item->company = Company::where('id', $item['contract']['company_id'])->get(['id', 'name'])[0];
                return $item;
            })
            ->toArray();
        $total = Service::where('status', "!=", '待审核')->count();
        $types = Service_type::all()->toArray();
        $sources = Service_source::all()->toArray();
        $data = [
            'data' => $services,
            'total' => $total,
            'types' => $types,
            'sources' => $sources
        ];
        return $this->res(200, '员工信息', $data);
    }

    /**
     * 筛选出待审核的服务单
     */
    public function verify()
    {
        $emp = Service::where('status','=','待审核');
        collect($emp)
            ->with(['customer', 'contract', 'source', 'type'])
            ->get()
            ->each(function ($ser){
                $ser->project_manager = $ser->contract->PM == null ? null : DB::select("select `id`, `name` from employees where id in ({$ser->contract->PM})");
            })
            ->toArray();
        $total = collect($emp)->count();
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
        $re = Service::findOrFail($id)->update([
            'status'=>'待派单'
        ]);
        return $this->res(200, '审核通过, 用户将收到通知');
    }

    public function rej($id)
    {
        $re = Service::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);
        return $this->res(200, '已拒绝, 用户将收到通知');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceStoreRequest $request)
    {
        //todo 前端右侧可以做一个派单


        //todo 检查响应时间
        if($request->status == "待审核" || $request->status == "已派单"){
            $request['time3'] = date('Y-m-d H:i:s', time());
        }

        //todo 存储
        $data = Service::create($request->all());
        return $this->res(2002, "新建信道服务单成功", $data);
    }

    //todo 派单时的方法及触发短信/邮件/内部通知
    public function waitingWork()
    {
        
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
    public function update(ServiceStoreRequest $request, $id)
    {
        $update = Service::find($id);

        //fixme 不支持修改合同单号, 所以前端只有灰色, 没有修改可能
        if($request->status == "待审核" && $update->status != "待审核"){  //变成待审核后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }
        if($request->status == "已派单" && $update->status != "已派单"){   //变成已派单后, 更新响应起始时间
            $request->time3 = date('Y-m-d H:i:s', time());
        }
        //todo 修改
        $re = $update->update($request->except(['contract','company', 'visits']));
        if($re){
            return $this->res(2003, "修改服务单成功");
        } else {
            return $this->res(500, "修改服务单失败");
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
        $re = Service::find($id)->delete();
        if($re){
            return $this->res(2004, "删除服务单成功");
        } else {
            return $this->res(500, "删除服务单失败");
        }
    }

    /**
     * 回访
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function visit(Request $request, $id)
    {
        $re = Service::findOrFail($id)->visits()->updateOrCreate($request->except(['employees', 'status','id']));
        if($re){
            return $this->res(2005, "填写回访成功");
        } else {
            return $this->res(500, "填写回访失败");
        }
    }

}
