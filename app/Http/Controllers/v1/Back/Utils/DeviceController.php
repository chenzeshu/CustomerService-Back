<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Repositories\ProblemRepo;
use App\Jobs\reportJob;
use App\Models\Channels\Channel_info2;
use App\Models\Utils\Device;
use App\Models\Utils\Profession;
use Illuminate\Http\Request;

class DeviceController extends ApiController
{
    private $problemRepo;

    public function __construct(ProblemRepo $problemRepo)
    {
        $this->problemRepo = $problemRepo;
    }

    public function page($page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $cons = Device::offset($begin)->limit($pageSize)
            ->with(['company', 'profession', 'channel_info2', 'problems.problemType'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
        $professions = Profession::all()->toArray();
        $info2s = Channel_info2::all()->toArray();
        $total = Device::count();
        $data = [
            'data' => $cons,
            'total' => $total,
            'professions' => $professions,
            'info2s' => $info2s
        ];
        return $this->res(200, '设备信息', $data);
    }

    public function store(Request $request)
    {
        $data = Device::create($request->except(['profession', 'company', 'channel_info2','created_at']));

        return $this->res(2002, "新建合同成功", ['data'=>$data]);
    }

    public function update(Request $request, $id)
    {
        $re = Device::find($id)->update($request->except(['profession', 'company', 'channel_info2','created_at']));
        if($re){
            return $this->res(2003, "修改合同成功");
        } else {
            return $this->res(-2003, "修改合同失败");
        }
    }

    public function destroy($id)
    {
        $re = Device::destroy($id);
        if($re){
            return $this->res(2004, "删除合同成功");
        } else {
            return $this->res(500, "删除合同失败");
        }
    }

    //要求关键字模糊查询
    public function search($name,  $page, $pageSize)
    {
        $begin = ( $page -1 ) * $pageSize;
        $emp = Device::where('device_id', 'like', '%'.$name.'%')
            ->offset($begin)
            ->limit($pageSize)
            ->get()
            ->toArray();

        $total = Device::where('device_id', 'like', '%'.$name.'%')
            ->count();

        $data= [
            'data'=> $emp,
            'total'=> $total,
        ];

        return $this->res(200, '搜索结果', $data);
    }

    public function report(Request $request)
    {
        $device_ids = [$request->device_id];
        $problem_id = $request->problem_id;
        $emp_ids = $request->emp_ids;

        list($problem, $emps, $data) = $this->problemRepo->pakReportData($device_ids, $problem_id, $emp_ids);

        //todo 发送预警短信
        $report_job = (new reportJob($emps, $data));
        $this->dispatch($report_job);

        //todo 记录本次报警
        $problem->reportRecords()->attach($device_ids);

        return $this->res(2002, '发送成功', $problem);
    }
}
