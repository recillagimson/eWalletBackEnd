<?php

namespace App\Services\Merchant;

use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\API\IApiService;
use App\Traits\Errors\WithSystemErrors;

class MerchantService implements IMerchantService
{
    private IEncryptionService $encryptionService;
    private IApiService $apiService;
    private $merchantUrl;
    private $merchantClientId;
    private $merchantClientSecret;

    use WithSystemErrors;

    public function __construct(
        IEncryptionService $encryptionService,
        IApiService $apiService
    )
    {
        $this->encryptionService = $encryptionService;
        $this->apiService = $apiService;
        $this->merchantUrl = config('merchant.merchant_url');
        $this->merchantClientId = config('merchant.merchant_client_id');
        $this->merchantClientSecret = config('merchant.merchant_client_secret');
    }

    public function list(array $attr) {
        // GENERATE CLIENT TOKEN
        $clientTokenResponse = $this->getClientToken();

        if($clientTokenResponse && $clientTokenResponse->status() == 200) {
            $clientToken = $clientTokenResponse->json()['access_token'];
            // GENERATE REQUEST ID
            $requestPayload = $this->getRequestId($clientToken);
            if($requestPayload && $requestPayload->status() ==200 && $requestPayload->json() && isset($requestPayload->json()['passPhrase'])) {
    
                $encrypted = $this->encryptionService->encrypt(json_encode($attr), $requestPayload->json()['passPhrase']);
                $getListUrl = $this->merchantUrl . "/merchant/list";
                $build = [
                    'id' => $requestPayload->json()['id'],
                    'payload' => json_encode($encrypted)
                ];
    
                $merchantListResponse = $this->apiService->post($getListUrl, $build, [
                    'Authorization' => 'Bearer ' . $clientToken,
                    'Accept' => 'application/json'
                ]);
    
                // HANDLE RESPONSE OF MERCHANT LIST
                if($merchantListResponse && $merchantListResponse->status() == 200) {
                    $decryptionResponse = $this->decryptResponse($merchantListResponse->json()['data'], $clientToken);
                    if($decryptionResponse && $decryptionResponse->status() == 200){
                        return $decryptionResponse->json();
                    }
                    // DECRYPTION ERROR
                    return $this->decryptionError();
                }
                // THROW ERROR GETTING MERCHANT LIST
                return $this->getMerchantListError();
            }
            // THROW ERROR GENERATE REQUEST ID
            return $this->requestIdError();
        }
        // THROW ERROR GETTING CLIENT TOKEN
        return $this->clientTokenError();
    }

    // REQUEST ID
    public function getRequestId(string $clientToken) {
        $requestIdUrl = $this->merchantUrl . "/payloads/generate";
        return $this->apiService->get($requestIdUrl, [
            'Authorization' => 'Bearer ' . $clientToken,
            'Accept' => 'application/json'
        ]);
    }
    // DECRYPT RESPONSE
    public function decryptResponse(array $response, string $clientToken) {
        $requestIdUrl = $this->merchantUrl . "/utils/decrypt";
        return $this->apiService->post($requestIdUrl, $response, [
            'Authorization' => 'Bearer '. $clientToken,
            'Accept' => 'application/json'
        ]);
    }
    // GENERATE CLIENT TOKEN
    public function getClientToken() {
        $requestIdUrl = $this->merchantUrl . "/clients/token";
        return $this->apiService->postAsForm($requestIdUrl, [
            'client_id' => $this->merchantClientId,
            'client_secret' => $this->merchantClientSecret,
        ], [
            'Authorization' => 'Bearer 127890|hUJbQA84ykfVHfGXVtyEwCGcVkd8iG92pcOBVxup',
            'Accept' => 'application/json'
        ]);
    }
}