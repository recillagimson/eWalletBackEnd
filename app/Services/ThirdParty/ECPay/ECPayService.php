<?php


namespace App\Services\ThirdParty\ECPay;


use App\Enums\TpaProviders;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithTpaErrors;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use App\Services\Utilities\XML\XmlService;

class ECPayService implements IECPayService
{
    use WithTpaErrors;

    private string $ecpayUrl;

    private string $username;
    private string $password;

    private IApiService $apiService;
    private array $defaultHeaders;

    public function __construct(IApiService $apiService)
    {

        $this->ecpayUrl = config('ecpay.ecpay_url');

        $this->username = config('ecpay.ecpay_username');
        $this->password = config('ecpay.ecpay_password');

        $this->apiService = $apiService;

        $this->defaultHeaders = [
            'Content-Type' => 'text/xml',
        ];
    }


    public function getBanks(): Response
    {
        dd($this->generateInstapayCheckStatusRequest());
        // $url = $this->ecpayUrl;
        // return $this->apiService->get($url, $this->defaultHeaders);
    }

    private function generateInstapayCheckStatusRequest(): string
    {
        $xmlService = new XmlService();

        $xmlService->startElement('FetchPayments');
        $xmlService->startElement('strdate', "08-13-2021");

        return $xmlService->getString();
    }

}
