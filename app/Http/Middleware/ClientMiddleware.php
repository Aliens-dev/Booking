<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if( (auth()->user()->user_role === 'admin') || 
            (auth()->user()->user_role === 'client' && auth()->user()->verified == 1)  
        ) 
        {
            return $next($request);
        }
        return response()->json(['success' => false,'message' => 'Unauthorized Access'], 401);
    }
}
