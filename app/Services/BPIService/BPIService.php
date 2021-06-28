<?php

namespace App\Services\BPIService;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
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

        $array['jti'] = Str::uuid()->toString();
        $array['iss'] = 'PARTNER';
        $array['sub'] = 'fundTopUp';
        $array['aud'] = 'BPI';
        $array['exp'] = Carbon::now()->addMinutes(30)->timestamp;
        $array['iat'] = Carbon::now()->timestamp;
        // dd($array);
        $jwt = $this->bpiEncodeJWT($array);
        // dd($jwt);
        $jwe = $this->bpiEncodeJWE($jwt);
        // $response_headers = $this->bpiDecryptionJWEHeader($jwe);
        
        // Send API request
        $response = $this->apiService->post(env('BPI_FUND_TOP_UP_ENDPOINT'), ['token' => $jwe], $token);
        
        $transactionId = $response->getHeaders()['transactionId']['0'];
        $jwt_response = $this->bpiDecryptionJWE($response['token']);
        $response_raw = $this->bpiDecryptionJWT($jwt_response);
        return [
            'response' => $response_raw,
            'transactionId' => $transactionId
        ];
    }

    public function bpiEncodeJWT(array $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();
        $pub = Storage::disk('local')->get('keys/squid.ph.key');

        $token = \Tmilos\JoseJwt\Jwt::encode($context, $payload, $pub, \Tmilos\JoseJwt\Jws\JwsAlgorithm::RS256, [
            "alg" => "RS256"
        ]);

        return $token;
    }

    public function bpiEncodeJWE(string $payload) {
        $factory = new \Tmilos\JoseJwt\Context\DefaultContextFactory();
        $context = $factory->get();

        $pub = Storage::disk('local')->get('keys/squid.ph.pub');
        $myPubKey = openssl_get_publickey($pub);
        $token = \Tmilos\JoseJwt\Jwe::encode($context, $payload, $myPubKey, \Tmilos\JoseJwt\Jwe\JweAlgorithm::RSA_OAEP, \Tmilos\JoseJwt\Jwe\JweEncryption::A128CBC_HS256, [
            "alg" => "RSA-OAEP",
            "enc"=> "A128CBC-HS256",
            "cty"=> "JWT"
        ]);

        return $token;
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
