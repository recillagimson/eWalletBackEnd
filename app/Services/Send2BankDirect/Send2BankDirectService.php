<?php


namespace App\Services\Send2BankDirect;

use App\Services\ThirdParty\UBP\IUBPService;

class Send2BankDirectService implements ISend2BankDirectService
{   
    private IUBPService $ubpService;

    public function __construct(IUBPService $ubpService)
    {
        $this->ubpService = $ubpService;
    }

    public function send2BankDirect() {
        $this->ubpService->send2BankDirect();
    }
}
