<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\contractc\ContractcRequest;
use App\Models\Contract_c;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;

//信道合同
class ContractcController extends Controller
{
    use PageTrait, ReturnTrait;

    public function page($page, $pageSize)
    {
        $data = $this->getPaginator($page, $pageSize);
        return $this->res(200, '信道合同', $data);
    }

    public function store(ContractcRequest $request)
    {
        $data = Contract_c::create($request->all());
        return $this->res(200, '新建信道合同成功', $data);
    }

    public function update(ContractcRequest $request, $id)
    {
        $re = Contract_c::findOrFail($id)->update($request->all());
        return $this->res(200, '更新信道合同成功', $re);
    }

    public function destroy($id)
    {
        $re = Contract_c::findOrFail($id)->delete();
        return $this->res(200, '删除成功', $re);
    }
}
