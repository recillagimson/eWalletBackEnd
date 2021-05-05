<?php


namespace App\Services\ThirdParty\BayadCenter;

use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;


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
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'scope' => $this->scopes
        ];

        return $response = Http::withBasicAuth(
            '5o2eg36qrrfmohroq1di6d94hs',
            'capgl33osf8m0gn8ohonch3aubij8pan3nalgl3hhhs5pjvl1ja'
        )->post(
            'https://stg.bc-api.bayad.com/v3/partners/oauth/token',
            [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'scope' => $this->scopes
            ]
        );

        $url = $this->baseUrl . $this->tokenUrl;
        $response = $this->apiService->postAsForm($url, $data);

        if (!$response->successful()) $this->tpaFailedAuthentication(TpaProviders::ubp);
        return (object)$response->json();
    }


}
