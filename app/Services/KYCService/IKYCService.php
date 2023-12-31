<?php
namespace App\Services\KYCService;

interface IKYCService {
    public function initFaceMatch(array $attr, bool $isPath = false);
    public function initMerchantFaceMatch(array $attr);
    public function initOCR(array $attr, $idType = '');
    public function checkIDExpiration(array $attr, $idType = 'phl_dl');
    public function matchOCR(array $attr);
    public function isEKYCValidated(array $params);
    public function handleCallback(array $attr);
    public function verify(array $attr, $from_api = true);
    public function verifyRequest(string $requestId);
    public function faceAuth(array $attr);
}
