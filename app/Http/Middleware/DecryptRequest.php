<?php

namespace App\Http\Middleware;

use App\Enums\ErrorCodes;
use App\Services\Encryption\IEncryptionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
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
                if(!$decryptedData) $this->payloadIsInvalid();

                $request->replace($decryptedData);
                return $next($request);
            }

            $this->payloadIsInvalid();
        }

        return $next($request);
    }

    private function payloadIsInvalid()
    {
        throw ValidationException::withMessages([
            'error_code' => ErrorCodes::PayloadInvalid,
            'payload' => 'Encrypted payload is invalid.'
        ]);
    }
}
