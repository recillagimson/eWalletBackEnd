<?php


namespace App\Services\ThirdParty\BayadCenter;

use App\Enums\PayBillsConfig;
use App\Enums\TpaProviders;
use App\Models\UserAccount;
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


    private function getToken(): object
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


    private function getAuthorizationHeaders(): array
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


    public function getOtherCharges(string $billerCode)
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->otherChargesUrl);
        return $this->apiService->get($url, $headers);
    }


    // For MECOP only
    public function getOtherChargesMECOP(string $billerCode, $data)
    {
        $headers = $this->getAuthorizationHeaders();
        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->otherChargesUrl) . '?amount='. $data['amount'];
        return $this->apiService->get($url, $headers);
    }

    
    public function validateAccount(string $billerCode, string $accountNumber,  $data): Response
    {
        $headers = $this->getAuthorizationHeaders();
        $otherCharges = $this->getOtherCharges($billerCode);

        // To catch MECOP biller and use another way to get the otherCharges
        if($billerCode === PayBillsConfig::MECOP) {
            $otherCharges = $this->getOtherChargesMECOP($billerCode, $data);
        } 

        $url = str_replace(':BILLER-CODE', $billerCode, $this->baseUrl . $this->verifyAccountUrl);
        $url = str_replace(':ACCOUNT-NUMBER', $accountNumber, $url);

       // if (!$otherCharges->successful()) return $otherCharges;    
        $data += array('paymentMethod' => 'CASH');
        $data += array('otherCharges' => $otherCharges['data']['otherCharges']);

        return $this->apiService->post($url, $data, $headers) ;
    }


    public function createPayment(string $billerCode, array $data, UserAccount $user)//: Response
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
