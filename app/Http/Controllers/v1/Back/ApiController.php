<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/17
 * Time: 15:38
 */

namespace App\Http\Controllers\v1\Back;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Chenzeshu\ChenUtils\Traits\PageTrait;
use Chenzeshu\ChenUtils\Traits\ReturnTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    use PageTrait, ReturnTrait;

    /**
     * github-服务器钩子
     * @param Request $request
     * @return bool
     */
    public function webhook(Request $request)
    {
        $signature = "sha1=".hash_hmac('sha1', $request->getContent(), env('WEBHOOK_SECRET_TOKEN'));

        if(strcmp($signature, $request->header('X-Hub-Signature')) == 0){
            system('deploy.sh');
        }else {
            return false;
        }
    }

    public function error($e){
        return response()->json([
            'msg'=>"错误",
        ], 500);
    }
}