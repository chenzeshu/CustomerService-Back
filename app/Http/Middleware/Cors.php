<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 设置允许访问的域地址
        $domains = ['http://localhost:8080', 'https://space-net.cn', 'https://www.space-net.cn'];
        // 判断请求头中是否包含ORIGIN字段

        if(isset($request->server()['HTTP_ORIGIN'])){
            $origin = $request->server()['HTTP_ORIGIN'];
            if (in_array($origin, $domains)) {
                //设置响应头信息
                header('Access-Control-Allow-Origin: '.$origin);
                header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Hub-Signature');
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE');
//                header('Access-Control-Allow-Methods: head, get, post, put, delete, patch ');
                header('Access-Control-Expose-Headers: Authorization, username, jwtToken');
            }
        }
        return $next($request);
    }
}
