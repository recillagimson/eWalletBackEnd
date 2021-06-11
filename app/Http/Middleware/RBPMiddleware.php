<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RBPMiddleware
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
        $exceptions = [];

        $current_route = \Route::getCurrentRoute()->getActionName();
        
        if(in_array($current_route, $exceptions)) {
            return $next($request);
        }

        if(request()->user() && request()->user()->role && request()->user()->role->permissions) {
            $permissions = request()->user()->role->permissions->pluck(['route_name']);
            if($permissions) {
                if(in_array($current_route, $permissions->toArray())) {
                    return $next($request);
                }
            }
        }
        throw ValidationException::withMessages([
            'permission_error' => 'Unauthorized Access'
        ]);
    }
}
