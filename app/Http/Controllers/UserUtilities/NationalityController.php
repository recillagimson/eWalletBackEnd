<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\Nationality\INationalityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\NationalityRequest;
use App\Models\UserUtilities\Nationality;
use App\Services\UserProfile\IUserProfileService;

class NationalityController extends Controller
{

    private IEncryptionService $encryptionService;
    private INationalityRepository $nationalityRepository;
    private IUserProfileService $userProfileService;
    
    public function __construct(INationalityRepository $nationalityRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService)
    {
        $this->nationalityRepository = $nationalityRepository;
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
        $records = $this->nationalityRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  NationalityRequest $request
     * @return JsonResponse
     */
    public function store(NationalityRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->nationalityRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function show(Nationality $nationality): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($nationality->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  NationalityRequest $request
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function update(NationalityRequest $request, Nationality $nationality): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $nationality);
        $updateRecord = $this->nationalityRepository->update($nationality, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Nationality $nationality
     * @return JsonResponse
     */
    public function destroy(Nationality $nationality): JsonResponse
    {
        $deleteRecord = $this->nationalityRepository->delete($nationality);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}