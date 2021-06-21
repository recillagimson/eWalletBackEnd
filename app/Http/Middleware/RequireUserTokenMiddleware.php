<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireUserTokenMiddleware
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
        if(request()->user()) {
            if(get_class(request()->user()) === 'App\Models\UserAccount') {
                return $next($request);
            }
        }

        return response()->json([
            'message' => config('permission.token_mismatch')
        ], 401);
    }
}
