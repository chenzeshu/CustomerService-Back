<?php

namespace App\Http\Controllers\v1\Back\Utils;

use App\Http\Controllers\v1\Back\ApiController;
use App\Models\Utils\Allow;

class AllowController extends ApiController
{
    public function report($allow_int)
    {
        $msg =  $allow_int == 1 ? "预警功能已启动" : "预警功能已关闭";

        $allow_tag = Allow::findOrFail(1)->update([
            'allow_report' => $allow_int
        ]);

        return $this->res(2003, $msg);
    }
}
