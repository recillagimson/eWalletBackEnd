<?php

namespace App\Services\KYCService;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\CurlService\ICurlService;

class KYCService implements IKYCService
{   
    private ICurlService $curlService;

    public function __construct(ICurlService $curlService)
    {
        $this->curlService = $curlService;
    }

    private function getAuthorizationHeaders(): array
    {
        $headers = array(
            'appId: ' . env('KYC_APP_ID'),
            'appKey: '. env('KYC_APP_KEY'),
            'accept: '. 'application/json',
            'content-type: ' . 'multipart/form-data'
        );
        return $headers;
    }

    public function initFaceMatch(array $attr) {
        $url = env('KYC_APP_FACEMATCH_URL');
        $headers = $this->getAuthorizationHeaders();

        $id = new \CURLFILE($attr['id_photo']->getPathname());
        $selfie = new \CURLFILE($attr['selfie_photo']->getPathname());

        $data = array('id' => $id, 'selfie' => $selfie);

        return $this->curlService->curlPost($url, $data, $headers);
    }

}
