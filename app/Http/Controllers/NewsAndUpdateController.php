<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\NewsAndUpdate\INewsAndUpdateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\NewsAndUpdate\NewsAndUpdateRequest;
use App\Models\NewsAndUpdate;

class NewsAndUpdateController extends Controller
{
    private IEncryptionService $encryptionService;
    private INewsAndUpdateRepository $newsAndUpdateRepository;
    
    public function __construct(INewsAndUpdateRepository $newsAndUpdateRepository,
                                IEncryptionService $encryptionService)
    {
        $this->newsAndUpdateRepository = $newsAndUpdateRepository;
        $this->encryptionService = $encryptionService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->newsAndUpdateRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
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
        $inputBody = $this->inputBody($details, $request->user());
        $createRecord = $this->newsAndUpdateRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
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
        return response()->json($encryptedResponse, Response::HTTP_OK);
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
        $inputBody = $this->inputBody($details, $request->user(), $news);
        $updateRecord = $this->newsAndUpdateRepository->update($news, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
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

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function inputBody(array $details, object $userAccount, object $news=null): array {
        if(!$news) {
            $details['user_created'] = $userAccount->id;
            $details['user_updated'] = $userAccount->id;
        }else {
            $details['user_updated'] = $userAccount->id;
        }
        return $details;
    }
}
