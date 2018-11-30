<?php

namespace App\Http\Controllers\v1\Back\Problems;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemType;
use Illuminate\Http\Request;

class ProblemController extends ApiController
{
    public function getPage($page, $pageSize, Request $request)
    {
        //todo 分页得到所有故障
        $offset = $pageSize * ($page - 1);

        $problems = Problem::with('problemType')->offset($offset)->limit(10)->get();
        $total = Problem::count();
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

    public function search($keywords)
    {
        //todo 考虑中：是否要es?
    }

}
