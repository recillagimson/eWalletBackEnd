<?php


namespace App\Services\ThirdParty\UBP;


use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\Client\Response;

class UBPService implements IUBPService
{
    use WithTpaErrors;

    private string $baseUrl;
    private string $tokenUrl;

    private string $pesonetTransferUrl;
    private string $pesonetTransactionUpdateUrl;
    private string $pesonetBanksUrl;

    private string $instaPayTransferUrl;
    private string $instaPayBanksUrl;

    private string $clientId;
    private string $clientSecret;
    private string $partnerId;
    private string $username;
    private string $password;
    private string $scopes;

    private IApiService $apiService;
    private array $defaultHeaders;

    public function __construct(IApiService $apiService)
    {
        $this->baseUrl = config('ubp.base_url');
        $this->tokenUrl = config('ubp.token_url');

        $this->pesonetTransferUrl = config('ubp.pesonet_transfer_url');
        $this->pesonetTransactionUpdateUrl = config('ubp.pesonet_transaction_update_url');
        $this->pesonetBanksUrl = config('ubp.pesonet_banks_url');

        $this->instaPayBanksUrl = config('ubp.instapay_banks_url');
        $this->instaPayTransferUrl = config('ubp.instapay_transfer_url');

        $this->clientId = config('ubp.client_id');
        $this->clientSecret = config('ubp.client_secret');
        $this->partnerId = config('ubp.partner_id');
        $this->username = config('ubp.username');
        $this->password = config('ubp.password');
        $this->scopes = config('ubp.scopes');

        $this->apiService = $apiService;

        $this->defaultHeaders = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'x-ibm-client-id' => $this->clientId,
            'x-ibm-client-secret' => $this->clientSecret,
            'x-partner-id' => $this->partnerId
        ];
    }

    /**
     * UBP Partner Authentication
     *
     * @return object
     */
    private function getToken(): object
    {
        $data = [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'username' => $this->username,
            'password' => $this->password,
            'scope' => $this->scopes
        ];

        $url = $this->baseUrl . $this->tokenUrl;
        $response = $this->apiService->postAsForm($url, $data);

        if (!$response->successful()) $this->tpaFailedAuthentication(TpaProviders::ubp);
        return (object)$response->json();
    }

    public function getBanks(string $provider): Response
    {
        $banksUrl = $provider === TpaProviders::ubpPesonet ? $this->pesonetBanksUrl : $this->instaPayBanksUrl;
        $url = $this->baseUrl . $banksUrl;
        return $this->apiService->get($url, $this->defaultHeaders);
    }


}
