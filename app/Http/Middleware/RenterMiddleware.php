<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class RenterMiddleware
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
        if((auth()->user() instanceof Admin) || (auth()->user()->user_role === 'renter')) {
            return $next($request);
        }
        return response()->json(['success' => false,'message' => 'Unauthorized Access'], 401);
    }
}
