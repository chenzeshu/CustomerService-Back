<?php

namespace App\Exceptions;

use Exception;

class TestException extends Exception
{
    /**
     * 报告异常
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * 将异常渲染到 HTTP 响应中。
     *
     * @param  \Illuminate\Http\Request
     * @return void
     */
    public function render()
    {
        return response('haha')->header('status', 404);
    }
}
