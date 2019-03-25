<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/6
 * Time: 17:53
 */

namespace App\Http\Repositories;


use App\Models\Employee;
use App\Models\Problem\Problem;
use App\Models\Utils\Device;

class ProblemRepo
{

    /**
     * 用于problem页面的筛选
     * @param $request
     * @return Problem|\Illuminate\Database\Eloquent\Builder | Problem模型的筛选结果，还没get
     */
    public function dealWithSearch($request)
    {
        $searchObj = $request->searchObj;

        if(!empty($searchObj['problem_type']['ptype_id'])){
            $problems = Problem::whereHas('problemType', function($query) use ($searchObj){
                $query->where('ptype_id', (int)$searchObj['problem_type']['ptype_id']);
            })
                ->with(['problemType', 'service']);
        } else {
            $problems = Problem::with(['problemType', 'service']);
        }

        if(!empty($searchObj['problem_step'])){
            $problems = $problems->where('problem_step', $searchObj['problem_step']);
        }

        if(!empty($searchObj['problem_urgency'])){
            $problems = $problems->where('problem_urgency', $searchObj['problem_urgency']);
        }

        if(!empty($searchObj['problem_importance'])){
            $problems = $problems->where('problem_importance', $searchObj['problem_importance']);
        }

        if(!empty($searchObj['problem_desc'])){
            $problems = $problems->where('problem_desc', $searchObj['problem_desc']);
        }

        return $problems;
    }


    /**
     * 组装报警所需数据
     * 被报警人
     * 相关设备
     * 故障描述
     */
    public function pakReportData($device_ids, $problem_id, $emp_ids)
    {
        //todo 获得故障模型
        $problem = Problem::findOrFail($problem_id);

        //todo 获得设备模型
        $device_name = Device::whereIn('id', $device_ids)
            ->get(['device_id'])
            ->map(function ($device){
                return $device->device_id;
            })
            ->implode(' 、 ');
        //todo 获得被通知人
        $emps = Employee::findOrFail($emp_ids);

        //todo 组装数据
        $data = [
            'device_name' => $device_name,
            'problem_desc' => $problem->problem_desc,
            'four00tel' => env('FOUR00TEL')
        ];

        return [$problem, $emps, $data];
    }
}