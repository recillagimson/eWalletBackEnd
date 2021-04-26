<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserPhoto\VerificationRequest;
use App\Services\Utilities\Verification\IVerificationService;

class UserPhotoController extends Controller
{
    private IEncryptionService $encryptionService;
    private IVerificationService $iVerificationService;


    public function __construct(IEncryptionService $encryptionService, 
                                IVerificationService $iVerificationService)
    {
        $this->encryptionService = $encryptionService;
        $this->iVerificationService = $iVerificationService;
    }


    public function createVerification(VerificationRequest $request) {
        $params = $request->all();
        $params['user_account_id'] = request()->user()->id;
        $createRecord = $this->iVerificationService->create($params);
        $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    public function createSelfieVerification() {
        
    }
}
