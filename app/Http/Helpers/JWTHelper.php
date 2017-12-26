<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15
 * Time: 8:46
 */

namespace App\Http\Helpers;

use App\User;
use Illuminate\Http\Request;

class JWTHelper
{
    /**
     *   从jwt-token中取得user_id并检索数据库
     *   返回检索到的$user实例
     */
    public static function getPayload(Request $request)
    {
        $token = $request->header('authorization');
        $pattern = "/Bearer /";
        $token = preg_replace($pattern, "", $token);
        $token = explode(".", $token);
        $payload = $token[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
        return $payload;
    }

    /**
     * fixme 应该把scope放进jwt, 避免调阅数据库
     * @param Request $request
     * @return mixed
     */
    public static function getUser(Request $request)
    {
        $payload = self::getPayload($request);
        $user_id = $payload['sub'];
        $user = User::find($user_id);
        return $user;
    }

    public static function getUserId(Request $request)
    {
        $user = self::getUser($request);
        return $user->id;
    }

    public static function getUserScope(Request $request)
    {
        $payload = self::getPayload($request);
        return $payload['scope'];
    }
}