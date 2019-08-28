<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard('api')->guest() && $this->auth->guard('staff_api')->guest()) {
            return response()->json((['status' => 401, 'message' => 'Unauthorized']), 401);
        }

/*
        $guards = array_keys(config('auth.guards'));
        foreach ($guards as $guard) {
            if ($user = $this->auth->guard($guard)->user()) {
                $request->merge([ 'current_user' => $user ]);
                return $next($request);
            }
        }
        return response()->json((['status' => 401, 'message' => 'Unauthorized']), 401);

        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([ 'message' => '该用户不存在。', 'status_code' => 404 ], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([ 'message' => 'Token已过期。', 'status_code' => 401 ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([ 'message' => 'Token无效。', 'status_code' => 401 ], 401); 
        } catch (JWTException $e) {
            return response()->json([ 'message' => '解析Token异常。', 'status_code' => 401 ], 401);
        }
*/
        return $next($request);
    }
}
