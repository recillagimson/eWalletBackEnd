<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Enums\AtmPrepaidResponseCodes;
use App\Enums\TopupTypes;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Traits\Errors\WithBuyLoadErrors;
use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AtmService implements IAtmService
{
    use WithTransactionErrors, WithBuyLoadErrors;

    private IReferenceNumberService $referenceNumberService;

    private string $id;
    private string $uid;
    private string $password;
    private string $keyPassword;

    private string $baseUrl;
    private string $productsUrl;
    private string $prefixUrl;
    private string $telcoStatusUrl;
    private string $balanceUrl;
    private string $topupUrl;
    private string $topupInquiryUrl;
    private string $epinUrl;
    private string $epinInquiryUrl;
    private IApiService $apiService;

    public function __construct(IApiService             $apiService,
                                IReferenceNumberService $referenceNumberService)
    {
        $this->id = config('services.load.atm.id');
        $this->uid = config('services.load.atm.uid');
        $this->password = config('services.load.atm.password');
        $this->keyPassword = config('services.load.atm.key_password', '');

        $this->baseUrl = config('services.load.atm.url');
        $this->productsUrl = config('services.load.atm.products_url');
        $this->prefixUrl = config('services.load.atm.prefix_url');
        $this->telcoStatusUrl = config('services.load.atm.telco_status_url');
        $this->balanceUrl = config('services.load.atm.balance_url');
        $this->topupUrl = config('services.load.atm.topup_url');
        $this->topupInquiryUrl = config('services.load.atm.topup_inquiry_url');
        $this->epinUrl = config('services.load.atm.topup_epin_url');
        $this->epinInquiryUrl = config('services.load.atm.topup_epin_inquiry_url');

        $this->referenceNumberService = $referenceNumberService;
        $this->apiService = $apiService;
    }

    public function getEpinProducts(): array
    {
        $data = $this->createATMPostBody();
        $headers = $this->getHeaders($data);

        $url = $this->baseUrl . $this->productsUrl;
        $response = $this->apiService->post($url, $data, $headers);

        if ($response->successful()) {
            $data = $response->json();
            $state = $data['responseCode'];
            if ($state === AtmPrepaidResponseCodes::requestReceived) {
                return $data['data'];
            }
        }

        Log::error('Get ATM Products Error', $response->json());
        return [];
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
            $state = $data['responseCode'];

            if ($state === AtmPrepaidResponseCodes::requestReceived) {
                $prefixes = collect($data['data']);

                $prefix = $prefixes->firstWhere('prefix', $prefix);
                if (!$prefix) $this->prefixNotSupported();

                // return $prefix['provider'];
                return ucfirst(strtolower($prefix['provider']));
            }
        }

        Log::error('ATM getProvider API Error', $response->json());
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
            $state = $data['responseCode'];
            if ($state === AtmPrepaidResponseCodes::requestReceived) {
                $prefixes = collect($data['data']);
                return $prefixes->where('provider', $provider)->sortBy(['provider', 'productCode', 'denominations']);
            }
        }

        Log::error('ATM getProductsByProvider API Error', $response->json());
        $this->prefixNotSupported();
    }

    public function topup(string $productCode, string $mobileNumber, string $refNo, string $type): Response
    {
        $data = [
            'productCode' => $productCode,
            'mobileNo' => $mobileNumber,
            'agentRefNo' => $refNo
        ];
        $postData = $this->createATMPostBody($data);
        $headers = $this->getHeaders($postData);

        $requestUrl = $type === TopupTypes::load ? $this->topupUrl : $this->epinUrl;
        $url = $this->baseUrl . $requestUrl;

        Log::info("Request {$type}:", $postData);
        return $this->apiService->post($url, $postData, $headers);
    }

    public function checkStatus(string $refNo, string $type): Response
    {
        $data = [
            'agentRefNo' => $refNo
        ];
        $postData = $this->createATMPostBody($data);
        $headers = $this->getHeaders($postData);

        $inquiryUrl = $type === TopupTypes::load ? $this->topupInquiryUrl : $this->epinInquiryUrl;
        $url = $this->baseUrl . $inquiryUrl;
        return $this->apiService->post($url, $postData, $headers);
    }

    public function generateSignature(array $data): string
    {
        $privateKeyContent = Storage::disk('local')->get('/key/partnerid.private.pfx');
        openssl_pkcs12_read($privateKeyContent, $certs, $this->keyPassword);

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
