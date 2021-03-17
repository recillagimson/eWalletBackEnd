<?php

namespace App\Http\Middleware;

use App\Services\Encryption\IEncryptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DecryptRequest
{
    private $encryptionService;

    public function __construct(IEncryptionService $encService)
    {
        $this->encryptionService = $encService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->has('payload') && $request->filled('payload'))
        {
            $reqId = $request->input('id');
            $data = $request->input('payload');

            $decryptedData = $this->encryptionService->decrypt($data, $reqId);
            if(!$decryptedData) return response(Response::HTTP_UNPROCESSABLE_ENTITY);

            $requestData = json_decode($decryptedData, true);
            $request->replace($requestData);
        }

        return $next($request);
    }
}
