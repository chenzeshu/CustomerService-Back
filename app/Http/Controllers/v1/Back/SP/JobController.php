<?php

namespace App\Http\Controllers\v1\Back\SP;


use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;

class JobController extends ApiController
{
    /**
     * 列出与自己有关的服务单
     */
    public function showServiceList($page, $pageSize, $emp_id)
    {
        $data = ServiceDAO::getService($page, $pageSize, $emp_id);
        if(empty($data)){
            return $this->res(7000, '暂无服务');
        }
        return $this->res(7001, '服务信息', $data);
    }

}
