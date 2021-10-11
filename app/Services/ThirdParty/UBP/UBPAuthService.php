<?php

namespace App\Services\ThirdParty\UBP;

class UBPAuthService
{
    private IUBPService $ubpService;

    public function __construct(IUBPService $ubpService)
    {
        $this->ubpService = $ubpService;
    }

    public function linkAccount(string $code)
    {
        $response = $this->ubpService->generateAccountToken($code);

        if ($response->successful()) {

        }
    }
}
