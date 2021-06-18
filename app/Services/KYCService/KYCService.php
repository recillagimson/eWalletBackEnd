<?php

namespace App\Services\KYCService;

use App\Repositories\UserAccount\IUserAccountRepository;
use App\Repositories\UserPhoto\IUserSelfiePhotoRepository;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\Utilities\API\IApiService;
use App\Services\Utilities\CurlService\ICurlService;
use Carbon\Carbon;

class KYCService implements IKYCService
{   
    private ICurlService $curlService;
    private IUserSelfiePhotoRepository $userSelfiePhotoRepository;
    private IUserAccountRepository $userAccountRepository;

    public function __construct(ICurlService $curlService, IUserSelfiePhotoRepository $userSelfiePhotoRepository, IUserAccountRepository $userAccountRepository)
    {
        $this->curlService = $curlService;
        $this->userSelfiePhotoRepository = $userSelfiePhotoRepository;
        $this->userAccountRepository = $userAccountRepository;
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

    public function initFaceMatch(array $attr, bool $isPath = false) {
        $url = env('KYC_APP_FACEMATCH_URL');
        $headers = $this->getAuthorizationHeaders();

        if($isPath) {
            $id = new \CURLFILE($attr['id_photo']);
            $selfie = new \CURLFILE($attr['selfie_photo']);
        } else {
            $id = new \CURLFILE($attr['id_photo']->getPathname());
            $selfie = new \CURLFILE($attr['selfie_photo']->getPathname());
        }

        $data = array('id' => $id, 'selfie' => $selfie);

        return $this->curlService->curlPost($url, $data, $headers);
    }

    public function initOCR(array $attr) {
        $url = env('KYC_APP_OCR_URL');
        $headers = $this->getAuthorizationHeaders();

        $headers[4] = "transactionId: " . (string)Str::uuid();

        $id = new \CURLFILE($attr['id_photo']->getPathname());

        $data = array('id' => $id);
        return $this->curlService->curlPost($url, $data, $headers);
    }

    public function initMerchantFaceMatch(array $attr) {

        $user = $this->userAccountRepository->getUserByAccountNumber($attr['account_number']);
        $selfie = $this->userSelfiePhotoRepository->getSelfieByAccountNumber($user->id);

        $selfie_s3 = Storage::disk('s3')->temporaryUrl($selfie->photo_location, Carbon::now()->addMinutes(10));

        $filename = Str::random(20);
        $tempImage = tempnam(sys_get_temp_dir(), $filename);
        copy($selfie_s3, $tempImage);

        $url = env('KYC_APP_FACEMATCH_URL');
        $headers = $this->getAuthorizationHeaders();

        $selfie_retrieved = new \CURLFILE($tempImage);
        $selfie = new \CURLFILE($attr['selfie_photo']->getPathname());

        $data = array('id' => $selfie_retrieved, 'selfie' => $selfie);
        
        $reponse = $this->curlService->curlPost($url, $data, $headers);
        unlink($tempImage);
        return $reponse;
    }

}
