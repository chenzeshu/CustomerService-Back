<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class getUserFromToken
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
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'errcode' => 400004,
                    'errmsg' => 'user not found'
                ], 404);
            }
        dd($user);
        } catch (TokenExpiredException $e) {

            return response()->json([
                'errcode' => 400001,
                'errmsg' => 'token expired'
            ], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json([
                'errcode' => 400003,
                'errmsg' => 'token invalid'
            ], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json([
                'errcode' => 400002,
                'errmsg' => 'token absent'
            ], $e->getStatusCode());

        }
        return $next($request);
    }
}
