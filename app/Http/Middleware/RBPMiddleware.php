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
        $exceptions = [
            
        ];

        $current_route = \Route::getCurrentRoute()->getActionName();
        
        if(in_array($current_route, $exceptions)) {
            return $next($request);
        }
        if(request()->user() && request()->user()->roles && request()->user()->roles) {

            $permissions = [];
            foreach(request()->user()->roles as $role) {
                $permissions = array_merge($permissions, $role->permissions->pluck(['route_name'])->toArray());
            }


            if($permissions) {
                if(in_array($current_route, $permissions)) {
                    return $next($request);
                }
            }
        }
        return response()->json([
            'message' => config('permission.unauthorized_access')
        ], 403);
    }
}
