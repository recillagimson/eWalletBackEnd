<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\SignupHost\ISignupHostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\SignupHostRequest;
use App\Models\UserUtilities\SignupHost;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;

class SignupHostController extends Controller
{

    private IEncryptionService $encryptionService;
    private ISignupHostRepository $signupHostRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    
    public function __construct(ISignupHostRepository $signupHostRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->signupHostRepository = $signupHostRepository;
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->signupHostRepository->getAll();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SignupHostRequest $request
     * @return JsonResponse
     */
    public function store(SignupHostRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->signupHostRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function show(SignupHost $signup_host): JsonResponse
    {
        // $encryptedResponse = $this->encryptionService->encrypt($signup_host->toArray());
        return $this->responseService->successResponse($signup_host->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SignupHostRequest $request
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function update(SignupHostRequest $request, SignupHost $signup_host): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $signup_host);
        $updateRecord = $this->signupHostRepository->update($signup_host, $inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SignupHost $signup_host
     * @return JsonResponse
     */
    public function destroy(SignupHost $signup_host): JsonResponse
    {
        $deleteRecord = $this->signupHostRepository->delete($signup_host);

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
