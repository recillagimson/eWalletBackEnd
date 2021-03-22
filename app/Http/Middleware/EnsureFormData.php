<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureFormData
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $contentType = $request->getContentType();
        if($contentType)
        {
            if($contentType === 'form')
            {
                return $next($request);
            }
        }

        return response(null,Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }
}
