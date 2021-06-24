<?php

namespace App\Services\BPIService;

use App\Enums\ReferenceNumberTypes;
use App\Traits\Errors\WithUserErrors;
use Illuminate\Support\Facades\Storage;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\ReferenceNumber\IReferenceNumberService;

class BPIService implements IBPIService
{
    use WithUserErrors;

    private IApiService $apiService;
    private IReferenceNumberService $referenceNumberService;


    public function __construct(IApiService $apiService, IReferenceNumberService $referenceNumberService)
    {
        $this->apiService = $apiService;
        $this->referenceNumberService = $referenceNumberService;
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
        $response = $this->apiService->get(env('BPI_TRANSACTIONAL_ENDPOINT'), $token)->json();

        if($response && isset($response['token'])) {
            $jwt = $this->bpiDecryptionJWE($response['token']);
            if($jwt) {
                return $this->bpiDecryptionJWT($jwt);
            }
        }

        // THROW ERROR
        $this->bpiTokenInvalid();
    }

    public function fundTopUp(Array $array, string $token) {
        $refNo = $this->referenceNumberService->generate(ReferenceNumberTypes::AddMoneyViaWebBank);
        $token = $this->getHeaders($token);
        $array['merchantTransactionReference'] = $refNo;
        // $response = $this->apiService->post(env('BPI_FUND_TOP_UP_ENDPOINT'), $token, $array)->json();

        $jwt = $this->bpiEncodeJWT($array);
        $jwe = $this->bpiEncodeJWE($jwt);
    }

    public function bpiEncodeJWT(array $array) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();
        $token = \Tmilos\JoseJwt\Jwt::encode($context, $array, null, \Tmilos\JoseJwt\Jws\JwsAlgorithm::NONE, []);
        return $token;
    }

    public function bpiEncodeJWE(string $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        // $myPubKey = openssl_get_privatekey($pub, '');
        $pub = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4e89sZv/VJztgxxrzdrI
        Oic0sEVkcHOuW5Urpgwo+hT8/iG40C269OC8uyC2527bYmVoslMtfbRuoc3q0sOc
        EZKR8vJWsCFh2VYtOPg2ZfImkotqE0QqaZHoC4KcWYTf7kNvbIyRxTUY4+PkS0lf
        d+0lEYu5WFvl9ocz8PO+6KnaTzW738z9DR9+L8/2Fl6yfLHvYqGkADCAubn0Zg3L
        WJNxWVsa0kUEjKBmHVO9b9rXkV2qsere9Dqu4QrOGamgT5aa/FWgUvWwQhcHNEgK
        bax/kn3iQ6nNavBITw4mHWItMVmzkozq8BsxsxA17GkGUVwqSjzMfM7gGpw3lAZ6
        5QIDAQAB";
        $token = \Tmilos\JoseJwt\Jwe::encode($context, $payload, $pub, \Tmilos\JoseJwt\Jwe\JweAlgorithm::DIR, \Tmilos\JoseJwt\Jwe\JweEncryption::A128CBC_HS256, []);
        dd($token);
    }

    private function bpiDecryptionJWE(string $payload) {        
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pri = Storage::disk('local')->get('keys/squid.ph.key');
        $myPrivateKey = openssl_get_privatekey($pri, '');

        $payload = \Tmilos\JoseJwt\Jwe::decode($context, $payload, $myPrivateKey);
        return $payload;
    }

    private function bpiDecryptionJWT(string $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $partyPublicKey = openssl_get_publickey($pub);

        $payload = \Tmilos\JoseJwt\JWT::decode($context, $payload, $partyPublicKey);
        return $payload;
    }
}
