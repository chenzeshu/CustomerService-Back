<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/17
 * Time: 15:38
 */

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use PageTrait, ReturnTrait;

    public function webhook(Request $request)
    {
        if($request->header('X-Hub-Signature') == 'chenzeshu'){
            system('deploy.sh');
        }else {
            return null;
        }
    }
}