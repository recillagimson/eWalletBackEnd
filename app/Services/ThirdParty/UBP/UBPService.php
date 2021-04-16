<?php


namespace App\Services\ThirdParty\UBP;


use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\Errors\IErrorService;

class UBPService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $partnerId;
    private string $username;
    private string $password;
    private string $scopes;

    private IApiService $apiService;
    private IErrorService $errorService;

    public function __construct(IApiService $apiService, IErrorService $errorService)
    {
        $this->baseUrl = config('ubp.base_url');
        $this->clientId = config('ubp.client_id');
        $this->clientSecret = config('ubp.client_secret');
        $this->partnerId = config('ubp.partner_id');
        $this->username = config('ubp.username');
        $this->password = config('ubp.password');
        $this->scopes = config('ubp.scopes');

        $this->apiService = $apiService;
        $this->errorService = $errorService;
    }

    private function getToken(): object
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => $this->scopes
        ];

        $url = $this->baseUrl.'/partners/v1/oauth2/token';
        $response = $this->apiService->postAsForm($url, $data);

        if(!$response->successful()) $this->errorService->tpaFailedAuthentication(TpaProviders::UBP);
        return (object) $response->json();
    }



}
