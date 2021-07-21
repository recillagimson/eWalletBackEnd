<?php


namespace App\Services\ThirdParty\DragonPay;


use App\Services\Utilities\API\IApiService;
use Illuminate\Http\Client\Response;

class DragonPayService implements IDragonPayService
{
    private string $baseUrl;
    private string $merchantId;
    private string $secretKey;

    private IApiService $apiService;

    public function __construct(IApiService $apiService)
    {
        $this->baseUrl = config('dragonpay.dp_base_url');
        $this->merchantId = config('dragonpay.dp_merchantID');
        $this->secretKey = config('dragonpay.dp_key');

        $this->apiService = $apiService;
    }

    public function generateUrl(string $refNo, string $email, string $fullName, float $amount): Response
    {
        $headers = $this->getHeaders();
        $url = $this->baseUrl . '/' . $refNo . '/post';

        $data = [
            'Amount' => $amount,
            'Currency' => 'PHP',
            'Description' => "Cashin for {$fullName}'s SquidPay account with an amount of: PHP {$amount}",
            'Email' => $email
        ];

        return $this->apiService->post($url, $data, $headers);
    }

    public function checkStatus(string $refNo): Response
    {
        $headers = $this->getHeaders();
        $url = $this->baseUrl . '/txnid/' . $refNo;
        return $this->apiService->get($url, $headers);
    }

    private function getHeaders(): array
    {
        $tokenText = utf8_encode("{$this->merchantId}:{$this->secretKey}");
        $token = base64_encode($tokenText);

        return [
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
            'Authorization' => 'Basic ' . $token
        ];
    }


}
