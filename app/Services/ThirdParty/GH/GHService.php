<?php

namespace App\Services\ThirdParty\GH;

use App\Services\Utilities\API\IApiService;
use Illuminate\Http\Client\Response;

class GHService implements IGHService
{
    private string $baseUrl;
    private string $printUrl;
    private string $apiKey;

    private IApiService $apiService;

    public function __construct(IApiService $apiService)
    {
        $this->baseUrl = config('ghapi.base_url');
        $this->printUrl = config('ghapi.print_url');
        $this->apiKey = config('ghapi.api_key');

        $this->apiService = $apiService;
    }

    public function print(array $data): Response
    {
        $data['APIKEY'] = $this->apiKey;
        $url = $this->baseUrl . $this->printUrl;

        return $this->apiService->post($url, $data);
    }
}
