<?php

namespace App\Services\BPIService;

use Jose\Component\Core\JWK;
use Jose\Component\Core\AlgorithmManager;
use App\Services\Utilities\API\IApiService;
use Jose\Component\Encryption\JWEDecrypter;
use Jose\Component\Encryption\Compression\Deflate;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Encryption\Serializer\CompactSerializer;
use Jose\Component\Encryption\Serializer\JWESerializerManager;
use Jose\Component\Encryption\Algorithm\KeyEncryption\RSAOAEP256;
use Jose\Component\Encryption\Compression\CompressionMethodManager;
use Jose\Component\Encryption\Algorithm\ContentEncryption\A128CBCHS256;

class BPIService implements IBPIService
{

    private IApiService $apiService;

    public function __construct(IApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    private function getHeaders(string $token) {
        return [
            'x-ibm-client-id' => 'fb5cedef-cfec-4910-9910-d40bc4f36752',
            'x-ibm-client-secret' => 'aC0rI2rN8qV0dX5dL3tG6bI2sY7xD4nO3lW5gF3aH4wT4wW8iO',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ];
    }

    public function getAccounts(string $token) {
        $token = $this->getHeaders($token);
        $response = $this->apiService->get("https://apitest.bpi.com.ph/bpi/api/accounts/transactionalAccounts", $token)->json();

        // dd($response);

        $key = "-----BEGIN PUBLIC KEY-----
        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4e89sZv/VJztgxxrzdrI
        Oic0sEVkcHOuW5Urpgwo+hT8/iG40C269OC8uyC2527bYmVoslMtfbRuoc3q0sOc
        EZKR8vJWsCFh2VYtOPg2ZfImkotqE0QqaZHoC4KcWYTf7kNvbIyRxTUY4+PkS0lf
        d+0lEYu5WFvl9ocz8PO+6KnaTzW738z9DR9+L8/2Fl6yfLHvYqGkADCAubn0Zg3L
        WJNxWVsa0kUEjKBmHVO9b9rXkV2qsere9Dqu4QrOGamgT5aa/FWgUvWwQhcHNEgK
        bax/kn3iQ6nNavBITw4mHWItMVmzkozq8BsxsxA17GkGUVwqSjzMfM7gGpw3lAZ6
        5QIDAQAB
        -----END PUBLIC KEY-----";
        
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        // RSA_OAEP - A128CBC-HS256
        // $token = \Tmilos\JoseJwt\Jwe::encode($context, $response['token'], $key, \Tmilos\JoseJwt\Jwe\JweAlgorithm::RSA_OAEP, \Tmilos\JoseJwt\Jwe\JweEncryption::A128CBC_HS256, []);

        $myPrivateKey = openssl_get_privatekey($key, '');
        // $partyPublicKey = openssl_get_publickey($key, '');

        // decode
        $payload = \Tmilos\JoseJwt\Jwe::decode($context, $response['token'], $myPrivateKey);
        dd($payload);
    }
}
