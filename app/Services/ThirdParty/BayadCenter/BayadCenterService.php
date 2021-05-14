<?php


namespace App\Services\ThirdParty\BayadCenter;

use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Str;

class BayadCenterService implements IBayadCenterService
{
    use WithTpaErrors;

    private string $baseUrl;
    private string $tokenUrl;

    private string $billersUrl;
    private string $billerInformationUrl;
    private string $otherChargesUrl;

    private string $verifyAccountUrl;
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

        $this->verifyAccountUrl = config('bc.validate_account_url');
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
            'Content-Type' => 'application/json',
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


    public function getBillerInformation(string $billerCode): Response 
    {
        $headers = $this->getAuthorizationHeaders();
        $url = $this->baseUrl . $this->billerInformationUrl . $billerCode;
        
        return $this->apiService->get($url, $headers);
    }

    
    public function getRequiredFields(string $billerCode)
    {
        $headers = $this->getAuthorizationHeaders();
        $url = $this->baseUrl . $this->billerInformationUrl . $billerCode;
        $billers = $this->apiService->get($url, $headers);

        $billers = json_decode($billers, true);
        $requiredFields = array();

        for ($x = 4; $x < count(array_keys($billers['data']['parameters']['verify'])); $x++) {
            $requiredFields[] = array_keys($billers['data']['parameters']['verify'][$x]);
        }
        
        return $this->dataFormat($requiredFields);
    }


    public function getOtherCharges(string $billerCode): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->otherChargesUrl);

        return $this->apiService->get($url, $headers);
    }


    public function verifyAccount(string $billerCode, string $accountNumber,  $data): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->verifyAccountUrl);
        $url = str_replace(':ACCOUNT-NUMBER', $accountNumber, $url);

        return $this->apiService->post($url, $data, $headers);
    }


    public function createPayment(string $billerCode, array $data): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->createPaymentUrl);
        $clientReference = array('clientReference' => (string) Str::uuid());

        return $this->apiService->post($url, array_merge($data, $clientReference), $headers);
    }


    public function inquirePayment(string $billerCode, string $clientReference): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->inquirePaymentUrl);
        $url = str_replace(':CLIENT-REFERENCE', $clientReference, $url);

       return $this->apiService->get($url, $headers);
    }

    
    public function getWalletBalance(): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $url = $this->baseUrl . $this->getWalletBalanceUrl;
        
        return $this->apiService->get($url, $headers);
    }



    // Private Methods
    private function dataFormat($requiredFields)
    {
        $data = "";
        $data = json_encode($requiredFields);
        $data = str_replace('[', '', $data);
        $data = str_replace(']', '', $data);
        $data = str_replace('otherInfo', '', $data);
        $data = str_replace('.', '', $data);
        $data = str_replace('"', '', $data);

        return explode(",", $data);
    }
    
}