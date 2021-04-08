<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\IVerificationService;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserPhoto\VerificationRequest;

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
        dd($request->all());
    }
}
