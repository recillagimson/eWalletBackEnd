<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\SourceOfFund\ISourceOfFundRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\SourceOfFundRequest;
use App\Models\UserUtilities\SourceOfFund;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use App\Enums\SuccessMessages;

class SourceOfFundController extends Controller
{

    private IEncryptionService $encryptionService;
    private ISourceOfFundRepository $sourceOfFundRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    
    public function __construct(ISourceOfFundRepository $sourceOfFundRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->sourceOfFundRepository = $sourceOfFundRepository;
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
        $records = $this->sourceOfFundRepository->getAllSourceOfFunds();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  SourceOfFundRequest $request
     * @return JsonResponse
     */
    public function store(SourceOfFundRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->sourceOfFundRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  SourceOfFund $source_of_fund
     * @return JsonResponse
     */
    public function show(SourceOfFund $source_of_fund): JsonResponse
    {
        // $encryptedResponse = $this->encryptionService->encrypt($source_of_fund->toArray());
        return $this->responseService->successResponse($source_of_fund->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SourceOfFundRequest $request
     * @param  SourceOfFund  $source_of_fund
     * @return JsonResponse
     */
    public function update(SourceOfFundRequest $request, SourceOfFund $source_of_fund): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $source_of_fund);
        $updateRecord = $this->sourceOfFundRepository->update($source_of_fund, $inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SourceOfFund $source_of_fund
     * @return JsonResponse
     */
    public function destroy(SourceOfFund $source_of_fund): JsonResponse
    {
        $deleteRecord = $this->sourceOfFundRepository->delete($source_of_fund);

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
