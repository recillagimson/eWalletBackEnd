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
    private string $instapayLibUrl;

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
        $this->instapayLibUrl = config('ubp.instapay_lib_url');

        $this->directUBPTransferUrl = config('ubp.direct_ubp_transfer_url');

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

    public function getPurposes(): Response
    {
        $url = $this->baseUrl . $this->instapayLibUrl . 'purpose';
        return $this->apiService->get($url, $this->defaultHeaders);
    }

    public function fundTransfer(string $refNo, string $fromFullName, string $zipCode, int $bankCode, string $recepientAccountNumber,
                                 string $recepientAccountName, float $amount, string $transactionDate,
                                 string $instructions, string $provider, string $purpose = "1003"): Response
    {
        $headers = $this->getAuthorizationHeaders();

        $data = [
            "senderRefId" => $refNo,
            "tranRequestDate" => $transactionDate,
            "sender" => [
                "name" => $fromFullName,
                "address" => [
                    "line1" => " ",
                    "line2" => " ",
                    "city" => " ",
                    "province" => " ",
                    "zipCode" => $zipCode,
                    "country" => " "
                ]
            ],
            "beneficiary" => [
                "accountNumber" => $recepientAccountNumber,
                "name" => $recepientAccountName,
                "address" => [
                    "line1" => "",
                    "line2" => "",
                    "city" => "",
                    "province" => "",
                    "zipCode" => "",
                    "country" => ""
                ]
            ],
            "remittance" => [
                "amount" => number_format($amount, 2),
                "currency" => "PHP",
                "receivingBank" => $bankCode,
                "purpose" => $purpose,
                "instructions" => $instructions
            ]
        ];

        $transferUrl = $provider === TpaProviders::ubpPesonet ? $this->pesonetTransferUrl : $this->instaPayTransferUrl;
        $url = $this->baseUrl . $transferUrl;
        return $this->apiService->post($url, $data, $headers);
    }

    public function checkStatus(string $provider, string $refNo): Response
    {
        $transferUrl = $provider === TpaProviders::ubpPesonet ? $this->pesonetTransferUrl : $this->instaPayTransferUrl;
        $url = $this->baseUrl . $transferUrl . '/' . $refNo;
        return $this->apiService->get($url, $this->defaultHeaders);
    }

    public function updateTransaction(string $status, string $remittanceId): Response
    {
        $headers = $this->getAuthorizationHeaders();

        $data = [
            'status' => $status,
            'remittanceId' => $remittanceId
        ];

        $updateUrl = $this->pesonetTransactionUpdateUrl;
        $url = $this->baseUrl . $updateUrl;

        return $this->apiService->post($url, $data, $headers);
    }

    private function getAuthorizationHeaders(): array
    {
        $token = $this->getToken();
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $token->access_token;

        return $headers;
    }


    public function send2BankUBPDirect(string $senderRefId, string $transactionDate, string $accountNo, float $amount, string $remarks, string $particulars, string $recipientName): Response
    {
        $token = $this->getToken();
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $token->access_token;

        $data = [
            "senderRefId" => $senderRefId,
            "tranRequestDate" => $transactionDate,
            "accountNo" => $accountNo,
            "amount" => [
                "currency" => "PHP",
                "value" => $amount
            ],
            "remarks" => $remarks,
            "particulars" => $particulars,
            "info" => [
                [
                    "index" => 1,
                    "name" => "Recipient",
                    "value" => $recipientName
                ]
            ]
        ];

        $transferUrl = $this->directUBPTransferUrl;
        $url = $this->baseUrl . $transferUrl;
        return $this->apiService->post($url, $data, $headers);
    }

    public function verifyPendingDirectTransaction(string $senderRefId) {
        $token = $this->getToken();
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $token->access_token;
        $transferUrl = $this->directUBPTransferUrl;
        $url = $this->baseUrl . $transferUrl . "/" . $senderRefId;
        return $this->apiService->get($url, $headers);
    }


}
