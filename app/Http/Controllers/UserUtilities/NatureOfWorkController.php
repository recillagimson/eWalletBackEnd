<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\NatureOfWork\INatureOfWorkRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\NatureOfWorkRequest;
use App\Models\UserUtilities\NatureOfWork;
use App\Services\UserProfile\IUserProfileService;

class NatureOfWorkController extends Controller
{

    private IEncryptionService $encryptionService;
    private INatureOfWorkRepository $natureOfWorkRepository;
    private IUserProfileService $userProfileService;
    
    public function __construct(INatureOfWorkRepository $natureOfWorkRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService)
    {
        $this->natureOfWorkRepository = $natureOfWorkRepository;
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
        $records = $this->natureOfWorkRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  NatureOfWorkRequest $request
     * @return JsonResponse
     */
    public function store(NatureOfWorkRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->natureOfWorkRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  NatureOfWork $nature_of_work
     * @return JsonResponse
     */
    public function show(NatureOfWork $nature_of_work): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($nature_of_work->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  NatureOfWorkRequest $request
     * @param  NatureOfWork $nature_of_work
     * @return JsonResponse
     */
    public function update(NatureOfWorkRequest $request, NatureOfWork $nature_of_work): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $nature_of_work);
        $updateRecord = $this->natureOfWorkRepository->update($nature_of_work, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(NatureOfWork $nature_of_work): JsonResponse
    {
        $deleteRecord = $this->natureOfWorkRepository->delete($nature_of_work);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
