<?php

namespace App\Http\Middleware\WhiteList;

use App\Models\WhiteList;
use Closure;
use Illuminate\Http\Request;

class WhiteListMiddleware
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
        $ip = request()->ip();
        $record = WhiteList::where('ip', $ip)->first();
        if($record) {
            return $next($request);
        }
        return response()->json([], 400);
    }
}
