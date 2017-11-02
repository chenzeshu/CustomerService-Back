<?php

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use App\User;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;

class UserController extends ApiController
{
    public function index()
    {
       $data = $this->getPaginator(1, 5);
        return $this->res(200, 'back-end users', $data);
    }

    public function page($page, $pageSize)
    {
        $data = $this->getPaginator($page, $pageSize);
        return $this->res(200, 'back-end users', $data);
    }

    public function show($id)
    {
        $data = User::find($id);
        return $this->res(200, 'user', $data);
    }
}
