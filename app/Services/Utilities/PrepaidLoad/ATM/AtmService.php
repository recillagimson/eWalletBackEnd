<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;
use App\Enums\ReferenceNumberTypes;

class AtmService implements IAtmService
{
    use WithTransactionErrors;
    private IReferenceNumberService $referenceNumberService;

    public function __construct(IReferenceNumberService $referenceNumberService)
    {
        $this->referenceNumberService = $referenceNumberService;
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
            $this->transFailed();
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

    public function showNetworkAndPrefix(): array
    {
        $signature = $this->generateSignature($this->createATMPostBody());

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').'/prefix-list', 
        $this->createATMPostBody());

        $result = json_decode($response->body());
        
        return $result->data;
    }

    public function showProductList(): array
    {
        $signature = $this->generateSignature($this->createATMPostBody());

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').'/product-list', 
        $this->createATMPostBody());

        $result = json_decode($response->body());
        
        return $result->data;
    }

    public function atmload(object $items): array
    {
        $referenceNumber = $this->referenceNumberService->generate(ReferenceNumberTypes::BuyLoad);
        $items->agentRefNo = $referenceNumber;
        $signature = $this->generateSignature($this->createATMPostBody($items));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Signature' => $signature
        ])->post(config('services.load.atm.url').' /topup-request', 
        $this->createATMPostBody($items));

        $result = json_decode($response->body());
        
        
        return $result->data;
    }

    public function createATMPostBody(object $items=null):array {
        $body = (object)[];

        $body->id = config('services.load.atm.id');
        $body->uid = config('services.load.atm.uid');
        $body->pwd = config('services.load.atm.password');
        $body->data = $items;

        return json_decode(json_encode($body), true);;
    }
}
