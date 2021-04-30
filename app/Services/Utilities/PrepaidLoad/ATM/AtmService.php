<?php


namespace App\Services\Utilities\PrepaidLoad\ATM;


use App\Traits\Errors\WithTransactionErrors;
use Illuminate\Support\Facades\Storage;

class AtmService implements IAtmService
{
    use WithTransactionErrors;

    public function __construct()
    {
    }

    public function load(array $items): array
    {
        // TODO: Implement load() method.
    }

    public function showNetworkPromos(): array
    {
        // TODO: Implement showNetworkPromos() method.
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
}
