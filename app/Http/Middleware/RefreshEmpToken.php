<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class RefreshEmpToken extends BaseMiddleware
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
        $response = $next($request);
        try {
            $token = $request->header('Authorization');
            $token = preg_replace("/^bearer\s/","",$token);
            Employee::authenticate($token);
            $newToken = $this->auth->setRequest($request)->parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        } catch (ModelNotFoundException $e){
            return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        // send the refreshed token back to the client
        $response->headers->set('Authorization', 'Bearer '.$newToken);
//        $response->headers->set('username', urlencode($user->name));
        return $response;
    }
}
