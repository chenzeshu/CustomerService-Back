<?php

namespace App\Http\Controllers\v1\Back\Problems;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Problem\Problem;
use App\Models\Problem\ProblemRecord;
use Illuminate\Http\Request;

class ProblemRecordController extends ApiController
{
    public function getPage($page, $pageSize)
    {
        $offset = $pageSize * ($page - 1);

        $data = ProblemRecord::with(['problem', 'device'])
                                ->offset($offset)
                                ->limit(10)
                                ->orderBy('created_at', 'desc')
                                ->get();

        $total = ProblemRecord::count();

        return $this->res(2000, '获取成功', ['data'=>$data, 'total' => $total]);
    }

    /**
     * 创建报警记录
     *      ----报警按钮在设备列表，拿故障库的模版直接可以报警
     * @param Request $request
     * @return bool
     */
    public function store(Request $request)
    {
        //fixme 本方法先放着，其实是不用的，给其他类参考
        ProblemRecord::create([
            'problem_id' => $request->problem_id,
            'device_id' => $request->device_id
        ]);
        return $this->res(2002, "新建成功");
    }

    //没有修改，报警记录只有被动创建和主动删除。

    public function delete($precord_id)
    {
        ProblemRecord::destroy($precord_id);
        return true;
    }
}
