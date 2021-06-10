<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $jwt = JWTAuth::parseToken()->authenticate();
        }catch (\Exception $exception) {
            if($exception instanceof TokenExpiredException) {
                return response()->json(['success' => false, 'message' => 'token expired'], 401);
            }else if($exception instanceof TokenInvalidException){
                return response()->json(['success' => false, 'message' => 'token invalid'], 401);
            }else {
                return response()->json(['success' => false, 'message' => 'token error'], 401);
            }
        }
        return $next($request);
    }
}
