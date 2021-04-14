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

class SourceOfFundController extends Controller
{

    private IEncryptionService $encryptionService;
    private ISourceOfFundRepository $sourceOfFundRepository;
    private IUserProfileService $userProfileService;
    
    public function __construct(ISourceOfFundRepository $sourceOfFundRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService)
    {
        $this->sourceOfFundRepository = $sourceOfFundRepository;
        $this->encryptionService = $encryptionService;
        $this->userProfileService = $userProfileService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->sourceOfFundRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
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

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  SourceOfFund $source_of_fund
     * @return JsonResponse
     */
    public function show(SourceOfFund $source_of_fund): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($source_of_fund->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
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

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
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

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}