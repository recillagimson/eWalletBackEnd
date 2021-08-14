<?php

namespace App\Services\Printing;

use App\Services\ThirdParty\GH\IGHService;
use App\Traits\Errors\WithTransactionErrors;

class PrintService implements IPrintService
{
    use WithTransactionErrors;

    private IGHService $ghService;

    public function __construct(IGHService $ghService)
    {
        $this->ghService = $ghService;
    }

    public function print(array $data)
    {
        $response = $this->ghService->print($data);
        if (!$response->successful()) {
            $this->transactionFailed();
        }
    }
}
