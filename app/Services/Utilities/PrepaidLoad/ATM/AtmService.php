<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Enums\AtmPrepaidResponseCodes;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithBuyLoadErrors;
use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AtmService implements IAtmService
{
    use WithTransactionErrors, WithBuyLoadErrors;

    private IReferenceNumberService $referenceNumberService;

    private string $id;
    private string $uid;
    private string $password;

    private string $baseUrl;
    private string $productsUrl;
    private string $prefixUrl;
    private string $telcoStatusUrl;
    private string $balanceUrl;
    private string $topupUrl;
    private string $topupInquiryUrl;
    private IApiService $apiService;

    public function __construct(IApiService $apiService,
                                IReferenceNumberService $referenceNumberService)
    {
        $this->id = config('services.load.atm.id');
        $this->uid = config('services.load.atm.uid');
        $this->password = config('services.load.atm.password');

        $this->baseUrl = config('services.load.atm.url');
        $this->productsUrl = config('services.load.atm.products_url');
        $this->prefixUrl = config('services.load.atm.prefix_url');
        $this->telcoStatusUrl = config('services.load.atm.telco_status_url');
        $this->balanceUrl = config('services.load.atm.balance_url');
        $this->topupUrl = config('services.load.atm.topup_url');
        $this->topupInquiryUrl = config('services.load.atm.topup_inquiry_url');

        $this->referenceNumberService = $referenceNumberService;
        $this->apiService = $apiService;
    }

    public function getProvider(string $mobileNumber)
    {
        $prefix = Str::substr($mobileNumber, 0, 4);
        $prefix = Str::replaceFirst('0', '63', $prefix);

        $data = $this->createATMPostBody();
        $headers = $this->getHeaders($data);

        $url = $this->baseUrl . $this->prefixUrl;
        $response = $this->apiService->post($url, $data, $headers);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['responseCode'] !== AtmPrepaidResponseCodes::requestReceived) {
                $prefixes = collect($data['data']);
                return $prefixes->firstWhere('prefix', $prefix)['provider'];
            }
        }

        $this->prefixNotSupported();
    }

    public function getProductsByProvider(string $provider): Collection
    {
        $data = $this->createATMPostBody();
        $headers = $this->getHeaders($data);

        $url = $this->baseUrl . $this->productsUrl;
        $response = $this->apiService->post($url, $data, $headers);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['responseCode'] !== AtmPrepaidResponseCodes::requestReceived) {
                $prefixes = collect($data['data']);
                return $prefixes->where('provider', $provider);
            }
        }

        $this->prefixNotSupported();
    }

    public function topupLoad(string $productCode, string $mobileNumber, string $refNo): Response
    {
        $data = [
            'productCode' => $productCode,
            'mobileNo' => $mobileNumber,
            'agentRefNo' => $refNo
        ];
        $postData = $this->createATMPostBody($data);
        $headers = $this->getHeaders($postData);

        $url = $this->baseUrl . $this->topupUrl;
        return $this->apiService->post($url, $postData, $headers);
    }

    public function checkStatus(string $refNo): Response
    {
        $data = [
            'agentRefNo' => $refNo
        ];
        $postData = $this->createATMPostBody($data);
        $headers = $this->getHeaders($postData);

        $url = $this->baseUrl . $this->topupInquiryUrl;
        return $this->apiService->post($url, $postData, $headers);
    }

    public function generateSignature(array $data): string
    {
        $privateKeyContent = Storage::disk('local')->get('/key/partnerid.private.pfx');
        openssl_pkcs12_read($privateKeyContent, $certs, '1234567890');

        $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        openssl_sign($jsonData, $signature, $certs['pkey'], OPENSSL_ALGO_SHA1);
        return base64_encode($signature);
    }

    public function verifySignature(array $data, string $base64Signature)
    {
        $signature = base64_decode($base64Signature);
        $cert = Storage::disk('local')->get('/key/partnerid.public.cer');
        $publicKeyId = openssl_get_publickey($cert);

        $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
        $result = openssl_verify($jsonData, $signature, $publicKeyId, OPENSSL_ALGO_SHA1);

        if ($result !== 1)
            $this->transactionFailed();
    }

    public function generatePEM(string $publicCertContent)
    {
        /* Convert .cer to .pem, cURL uses .pem */
        $certificateCApemContent = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($publicCertContent), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;

        $certificateCApem = 'partnerid.public' . '.pem';
        Storage::disk('local')->put('/key/' . $certificateCApem, $certificateCApemContent);
    }

    public function convertMobileNumberPrefixToAreaCode(string $mobileNo): string
    {
        $strSplit = str_split($mobileNo);
        if (intval($strSplit[0]) == 0) {
            $strSplit[0] = '63';
        }

        return join("", $strSplit);
    }

    private function getHeaders(array $data): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Signature' => $this->generateSignature($data)
        ];
    }

    private function createATMPostBody(array $data = null): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'pwd' => $this->password,
            'data' => $data
        ];
    }
}
