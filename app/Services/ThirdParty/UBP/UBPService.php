<?php


namespace App\Services\ThirdParty\UBP;


use App\Enums\TpaProviders;
use App\Models\UBP\UbpAccountToken;
use App\Repositories\UBPAccountToken\IUBPAccountTokenRepository;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UBPService implements IUBPService
{
    use WithTpaErrors;

    private string $baseUrl;
    private string $partnerTokenUrl;

    private string $customerAuthorizeUrl;
    private string $customerTokenUrl;
    private string $customerRedirectUrl;
    private string $customerScopes;

    private string $pesonetTransferUrl;
    private string $pesonetTransactionUpdateUrl;
    private string $pesonetBanksUrl;

    private string $instaPayTransferUrl;
    private string $instaPayBanksUrl;
    private string $instapayLibUrl;

    private string $merchantPaymentUrl;

    private string $clientId;
    private string $clientSecret;
    private string $partnerId;
    private string $username;
    private string $password;
    private string $scopes;

    private IApiService $apiService;
    private IUBPAccountTokenRepository $ubpTokens;

    private array $defaultHeaders;

    public function __construct(IApiService $apiService, IUBPAccountTokenRepository $ubpTokens)
    {
        $this->baseUrl = config('ubp.base_url');
        $this->partnerTokenUrl = config('ubp.partner_token_url');

        $this->customerAuthorizeUrl = config('ubp.customer_authorize_url');
        $this->customerTokenUrl = config('ubp.customer_token_url');
        $this->customerRedirectUrl = config('ubp.customer_redirect_url');
        $this->customerScopes = config('ubp.customer_scopes');

        $this->pesonetTransferUrl = config('ubp.pesonet_transfer_url');
        $this->pesonetTransactionUpdateUrl = config('ubp.pesonet_transaction_update_url');
        $this->pesonetBanksUrl = config('ubp.pesonet_banks_url');

        $this->instaPayBanksUrl = config('ubp.instapay_banks_url');
        $this->instaPayTransferUrl = config('ubp.instapay_transfer_url');
        $this->instapayLibUrl = config('ubp.instapay_lib_url');

        $this->merchantPaymentUrl = config('ubp.merchant_payment_url');
        $this->directUBPTransferUrl = config('ubp.direct_ubp_transfer_url');

        $this->clientId = config('ubp.client_id');
        $this->clientSecret = config('ubp.client_secret');
        $this->partnerId = config('ubp.partner_id');
        $this->username = config('ubp.username');
        $this->password = config('ubp.password');
        $this->scopes = config('ubp.scopes');

        $this->apiService = $apiService;
        $this->ubpTokens = $ubpTokens;

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

        $url = $this->baseUrl . $this->partnerTokenUrl;
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

    public function fundTransfer(
        string $refNo,
        string $fromFullName,
        string $zipCode,
        int $bankCode,
        string $recipientAccountNumber,
        string $recipientAccountName,
        float $amount,
        string $transactionDate,
        string $instructions,
        string $provider,
        string $purpose = "1003"
    ): Response {
        $headers = $this->getAuthorizationHeaders();

        $data = [
            "senderRefId" => $refNo . 'DEV001',
            "tranRequestDate" => $transactionDate,
            "sender" => [
                "name" => $this->formatName($fromFullName),
                "address" => [
                    "line1" => "Metro Manila",
                    "line2" => " ",
                    "city" => " ",
                    "province" => " ",
                    "zipCode" => $zipCode,
                    "country" => "PH"
                ]
            ],
            "beneficiary" => [
                "accountNumber" => $recipientAccountNumber,
                "name" => $this->formatName($recipientAccountName),
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
                "amount" => number_format($amount, 2, '.', ''),
                "currency" => "PHP",
                "receivingBank" => $bankCode,
                "purpose" => $purpose,
                "instructions" => $instructions
            ]
        ];

        Log::info('Fund Transfer Payload ' . $provider . ':', $data);
        $json = json_encode($data);

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

    public function send2BankUBPDirect(
        string $senderRefId,
        string $transactionDate,
        string $accountNo,
        float $amount,
        string $remarks,
        string $particulars,
        string $recipientName
    ): Response {
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

    public function verifyPendingDirectTransaction(string $senderRefId): Response
    {
        $token = $this->getToken();
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $token->access_token;
        $transferUrl = $this->directUBPTransferUrl;
        $url = $this->baseUrl . $transferUrl . "/" . $senderRefId;
        return $this->apiService->get($url, $headers);
    }

    public function merchantPayment(string $userToken, array $data): Response
    {
        $headers = $this->defaultHeaders;
        $headers['Authorization'] = 'Bearer ' . $userToken;

        $data = [
            'senderRefId' => $data['reference_number'],
            'tranRequestDate' => $data['transaction_date'],
            'amount' => [
                'currency' => 'PHP',
                'value' => $data['amount']
            ],
            "remarks" => 'SquidPay Add Money Transaction',
            "particulars" => "SquidPay Add Money Transaction",
            "info" => [
                [
                    "index" => 1,
                    "name" => "SquidPayAccountId",
                    "value" => $data['user_account_id'],
                ],
                [
                    "index" => 2,
                    "name" => "Name",
                    "value" => $data['user_full_name'],
                ],
                [
                    "index" => 3,
                    "name" => "ReferenceNumber",
                    "value" => $data['reference_number'],
                ],
            ]
        ];

        $url = $this->baseUrl . $this->merchantPaymentUrl;
        return $this->apiService->post($url, $data, $headers);
    }

    public function checkMerchantPaymentStatus(string $refNo): Response
    {
        $url = $this->baseUrl . $this->merchantPaymentUrl . "/$refNo";
        return $this->apiService->get($url, $this->defaultHeaders);
    }

    public function generateAuthorizeUrl(): string
    {
        $url = $this->baseUrl . $this->customerAuthorizeUrl . '?';
        $url .= 'client_id=' . urlencode($this->clientId) . '&';
        $url .= 'response_type=' . urlencode('code') . '&';
        $url .= 'redirect_url=' . urlencode($this->customerRedirectUrl) . '&';
        $url .= 'scope=' . urlencode($this->customerScopes) . '&';
        $url .= 'type=linking&';
        $url .= 'partnerId=' . urlencode($this->partnerId);

        return $url;
    }

    public function generateAccountToken(string $userId, string $code): array
    {
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'code' => $code,
            'redirect_uri' => $this->customerRedirectUrl
        ];

        $url = $this->baseUrl . $this->customerTokenUrl;
        $response = $this->apiService->postAsForm($url, $data);
        $data = $response->json();

        if (!$response->successful()) {
            Log::error('UBP Customer Token Error:', $data);
            $this->tpaFailedAuthentication(TpaProviders::ubp);
        }

        $token = $this->createUbpToken($userId, $data);
        return $token->toArray();
    }

    private function formatName(string $name): string
    {
        $formattedName = Str::replace("-", " ", $name);
        $formattedName = Str::replace("ñ", "n", $formattedName);
        return Str::replace("Ñ", "N", $formattedName);
    }

    private function createUbpToken($userId, array $data): UbpAccountToken
    {
        $token = $this->ubpTokens->getByUser($userId);
        if ($token) $this->ubpTokens->delete($token);

        $token = [
            'user_account_id' => $userId,
            'token_type' => $data['token_type'],
            'access_token' => $data['access_token'],
            'metadata' => $data['metadata'],
            'expires_in' => Carbon::createFromTimestamp($data['consented_on'])->addSeconds($data['expires_in']),
            'consented_on' => Carbon::createFromTimestamp($data['consented_on']),
            'scope' => $data['scope'],
            'refresh_token' => $data['refresh_token'],
            'refresh_token_expiration' => Carbon::createFromTimestamp($data['consented_on'])
                ->addSeconds($data['refresh_token_expires_in'])
        ];

        return $this->ubpTokens->create($token);
    }
}
