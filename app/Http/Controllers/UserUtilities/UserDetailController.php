<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserDetail\AddUpdateRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserDetail\IUserDetailService;

class UserDetailController extends Controller
{
    private IEncryptionService $encryptionService;
    private IUserDetailService $userDetailService;

    public function __construct(IEncryptionService $encryptionService, 
                                IUserDetailService $userDetailService)
    {
        $this->encryptionService = $encryptionService;
        $this->userDetailService = $userDetailService;
    }

    /**
     * Add or Update
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function addOrUpdate(Request $request, AddUpdateRequest $addUpdateRequestrequest): JsonResponse
    {
        $details = $addUpdateRequestrequest->validated();
        $addOrUpdate = $this->userDetailService->addOrUpdate($request->user(), $details);
        
        $encryptedResponse = $this->encryptionService->encrypt($addOrUpdate);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

}
