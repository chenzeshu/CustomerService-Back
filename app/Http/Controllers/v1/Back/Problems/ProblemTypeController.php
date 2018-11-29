<?php

namespace App\Http\Controllers\v1\Back\Problems;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Problem\ProblemType;
use Illuminate\Http\Request;

class ProblemTypeController extends ApiController
{
    public function getPage($page, $pageSize, Request $request)
    {
        //todo 分页得到所有故障
        $offset = $pageSize * ($page - 1);

        $problems = ProblemType::offset($offset)
            ->limit(10)
            ->get();
        $total = ProblemType::count();
        $data = [
            'data' => $problems,
            'total' => $total,
        ];
        return $this->res(2000, '获取成功', $data);
    }

    public function store(Request $request)
    {
        ProblemType::create([
           'ptype_name' => $request->ptype_name,
           'ptype_remark' => $request->ptype_remark
        ]);

        return $this->res(2002, '新建成功');
    }

    public function update($ptype_id, Request $request)
    {
        ProblemType::findOrFail($ptype_id)->update([
            'ptype_name' => $request->ptype_name,
            'ptype_remark' => $request->ptype_remark
        ]);

        return $this->res(2003, '更新成功');
    }

    public function delete($ptype_id)
    {
        ProblemType::destroy($ptype_id);
        return $this->res(2004, '删除成功');
    }

}
