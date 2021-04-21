<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\NewsAndUpdate\NewsAndUpdateRequest;
use App\Models\NewsAndUpdate;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;

class NewsAndUpdateController extends Controller
{
    private IEncryptionService $encryptionService;
    private INewsAndUpdateRepository $newsAndUpdateRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;
    
    public function __construct(INewsAndUpdateRepository $newsAndUpdateRepository,
                                IEncryptionService $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService $responseService)
    {
        $this->newsAndUpdateRepository = $newsAndUpdateRepository;
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
        $records = $this->newsAndUpdateRepository->getAll();

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function store(NewsAndUpdateRequest $request)
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->newsAndUpdateRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param NewsAndUpdate $id
     * @return JsonResponse
     */
    public function show(NewsAndUpdate $news): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($news->toArray());
        return $this->responseService->successResponse($news->toArray(), SuccessMessages::success);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param NewsAndUpdate $news
     * @param NewsAndUpdateRequest $request
     * @return JsonResponse
     */
    public function update(NewsAndUpdateRequest $request, NewsAndUpdate $news): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $news);
        $updateRecord = $this->newsAndUpdateRepository->update($news, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(NewsAndUpdate $news): JsonResponse
    {
        $deleteRecord = $this->newsAndUpdateRepository->delete($news);

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
