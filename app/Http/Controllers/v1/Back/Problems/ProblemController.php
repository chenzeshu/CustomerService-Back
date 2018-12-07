<?php

namespace App\Http\Controllers\v1\Back\Problems;

use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Repositories\MailRepository;
use App\Http\Repositories\ProblemRepo;
use App\Jobs\reportJob;
use App\Models\Employee;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
use App\Models\Utils\Device;
use Illuminate\Http\Request;

class ProblemController extends ApiController
{

    private $mailRepository;
    public function __construct(ProblemRepo $problemRepo, MailRepository $mailRepository)
    {
        $this->problemRepo = $problemRepo;
        $this->mailRepository = $mailRepository;
    }


    public function getPage($page, $pageSize, Request $request)
    {
        //todo 分页得到所有故障
        $offset = $pageSize * ($page - 1);
        //todo 处理筛选
        $problems = $this->problemRepo->dealWithSearch($request);

        //计算复合条件的总个数
        $total = $problems->count();
        //提供分页数据
        $problems = $problems->offset($offset)->limit(10)->get();

        $types = ProblemType::get(['ptype_id', 'ptype_name'])->toArray();
        $data = [
            'data' => $problems,
            'total' => $total,
            'types' => $types
        ];
        return $this->res(2000, '故障信息列表', $data);
    }

    /**
     * 本方法给故障列表页面主动创建，不关联具体设备和服务单
     * @param Request $request
     */
    public function store(Request $request)
    {
        Problem::create([
            'problem_type' => $request->problem_type,
            'problem_step' => $request->problem_step,
            'problem_desc' => $request->problem_desc,
            'problem_solution' => $request->problem_solution,
            'problem_urgency' => $request->problem_urgency,
            'problem_importance' => $request->problem_importance,
            'problem_remark' => $request->problem_remark,
        ]);
        return $this->res(2002, "新建成功");
    }


    /**
     * 同样不能修改具体设备与服务单，因为那些是自动关联的，也不用改。
     * @param $problem_id
     * @param Request $request
     */
    public function update($problem_id, Request $request)
    {
        Problem::findOrFail($problem_id)
            ->update([
            'problem_type' => $request->problem_type,
            'problem_step' => $request->problem_step,
            'problem_desc' => $request->problem_desc,
            'problem_solution' => $request->problem_solution,
            'problem_urgency' => $request->problem_urgency,
            'problem_importance' => $request->problem_importance,
            'problem_remark' => $request->problem_remark,
        ]);
        return $this->res(2003, "更新成功");
    }

    public function delete($problem_id)
    {
        Problem::destroy($problem_id);
        return $this->res(2004, "删除故障信息成功");
    }

    /**
     * 短信报警 + 记录
     * @param Request $request
     */
    public function report(Request $request)
    {
        $device_ids = $request->device_ids;
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



//        //todo 获得故障模型
//        $problem = Problem::findOrFail($problem_id);
//        //todo 获得设备模型
//        $device_name = Device::whereIn('id', $device_ids)
//            ->get(['device_id'])
//            ->map(function ($device){
//                return $device->device_id;
//            })
//            ->implode(' 、 ');
//        //todo 获得被通知人

//        $emps = Employee::findOrFail($emp_ids);
//
//        //todo 组装数据
//        $data = [
//            'device_name' => $device_name,
//            'problem_desc' => $problem->problem_desc,
//            'four00tel' => env('FOUR00TEL')
//        ];
//
//        foreach ($emps as $emp){
//            $this->mailRepository->sendReportMsg(
//                $emp->phone,
//                array_merge([
//                    "name" => $emp->name,
//                ],
//                    $data)
//            );
//        }
