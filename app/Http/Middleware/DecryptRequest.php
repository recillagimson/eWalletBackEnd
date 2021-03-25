<?php

namespace App\Http\Middleware;

use App\Services\Encryption\IEncryptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DecryptRequest
{
    private IEncryptionService $encryptionService;

    public function __construct(IEncryptionService $encService)
    {
        $this->encryptionService = $encService;
    }
    /**
     * Handle an incoming encrypted request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $method = $request->method();

        if($method === 'POST' || $method === 'PUT')
        {
            if($request->has('payload') && $request->filled('payload'))
            {
                $reqId = $request->input('id');
                $data = $request->input('payload');

                $decryptedData = $this->encryptionService->decrypt($data, $reqId);
                if(!$decryptedData) return response('',Response::HTTP_UNPROCESSABLE_ENTITY);

                $request->replace($decryptedData);
                return $next($request);
            }

            return response('',Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $next($request);
    }
}
