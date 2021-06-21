<?php

namespace App\Services\BPIService;

use App\Services\Utilities\API\IApiService;

class BPIService implements IBPIService
{

    private IApiService $apiService;

    public function __construct(IApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getAccounts(string $token) {
        $respose = $this->apiService->get("https://apitest.bpi.com.ph/bpi/api/accounts/transactionalAccounts", [
            'x-ibm-client-id' => 'fb5cedef-cfec-4910-9910-d40bc4f36752',
            'x-ibm-client-secret' => 'aC0rI2rN8qV0dX5dL3tG6bI2sY7xD4nO3lW5gF3aH4wT4wW8iO',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]);

        dd($respose->json());
    }
}
