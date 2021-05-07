<?php


namespace App\Services\ThirdParty\BayadCenter;

use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;

class BayadCenterService implements IBayadCenterService
{
    use WithTpaErrors;

    private string $baseUrl;
    private string $tokenUrl;

    private string $billersUrl;
    private string $billerInformationUrl;
    private string $otherChargesUrl;

    private string $validateAccountUrl;
    private string $createPaymentUrl;
    private string $inquirePaymentUrl;
    private string $getWalletBalanceUrl;

    private string $tpaId;
    private string $clientId;
    private string $clientSecret;
    private string $scopes;

    private IApiService $apiService;
    private array $defaultHeaders;

    public function __construct(IApiService $apiService)
    {
        $this->baseUrl = config('bc.base_url');
        $this->tokenUrl = config('bc.token_url');

        $this->billersUrl = config('bc.biller_url');
        $this->billerInformationUrl = config('bc.biller_information_url');
        $this->otherChargesUrl = config('bc.otherChargesUrl');

        $this->validateAccountUrl = config('bc.validate_account_url');
        $this->createPaymentUrl = config('bc.create_payment_url');
        $this->inquirePaymentUrl = config('bc.inquire_payment_url');
        $this->getWalletBalanceUrl = config('bc.get_wallet_balance_url');

        $this->tpaId = config('bc.tpa_id');
        $this->clientId = config('bc.client_id');
        $this->clientSecret = config('bc.client_secret');
        $this->scopes = config('bc.scopes');

        $this->apiService = $apiService;

        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'x-ibm-client-id' => $this->clientId,
            'x-ibm-client-secret' => $this->clientSecret,
        ];
    }

    public function getToken(): object
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'tpa_id' => $this->tpaId,
            'scope' => $this->scopes
        ];

        $url = $this->baseUrl . $this->tokenUrl;
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)->post($url, $data);

        if (!$response->successful()) $this->tpaFailedAuthentication(TpaProviders::bc);
        return (object)$response->json();
    }

    public function getAuthorizationHeaders(): array
    {
        $token = $this->getToken();
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $token->access_token;

        return $headers;
    }


    public function getBillers() : Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = $this->baseUrl . $this->billersUrl;
        return $this->apiService->get($url, $headers);
    }


}
