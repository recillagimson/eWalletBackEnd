<?php

namespace App\Services\BPIService;

use Jose\Easy\Load;
use Firebase\JWT\JWT;
use App\Services\Utilities\API\IApiService;

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
        $key = "-----BEGIN PUBLIC KEY-----
        MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4e89sZv/VJztgxxrzdrI
        Oic0sEVkcHOuW5Urpgwo+hT8/iG40C269OC8uyC2527bYmVoslMtfbRuoc3q0sOc
        EZKR8vJWsCFh2VYtOPg2ZfImkotqE0QqaZHoC4KcWYTf7kNvbIyRxTUY4+PkS0lf
        d+0lEYu5WFvl9ocz8PO+6KnaTzW738z9DR9+L8/2Fl6yfLHvYqGkADCAubn0Zg3L
        WJNxWVsa0kUEjKBmHVO9b9rXkV2qsere9Dqu4QrOGamgT5aa/FWgUvWwQhcHNEgK
        bax/kn3iQ6nNavBITw4mHWItMVmzkozq8BsxsxA17GkGUVwqSjzMfM7gGpw3lAZ6
        5QIDAQAB
        -----END PUBLIC KEY-----";
        
        // return $decoded = JWT::decode($response['token'], $key, array('HS256'));

        $jwt = Load::jwe($response['token']) // We want to load and decrypt the token in the variable $token
            ->algs(['RSA-OAEP-256', 'RSA-OAEP']) // The key encryption algorithms allowed to be used
            ->encs(['A256GCM']) // The content encryption algorithms allowed to be used
            ->exp()
            ->iat()
            ->nbf()
            ->aud('audience1')
            ->iss('issuer')
            ->sub('subject')
            ->jti('0123456789')
            ->key($key) // Key used to decrypt the token
            ->run(); // Go!

            dd($jwt);
;
    }
}
