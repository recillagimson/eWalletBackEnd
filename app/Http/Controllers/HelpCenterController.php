<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\HelpCenter\IHelpCenterRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\HelpCenter\HelpCenterRequest;
use App\Models\HelpCenter;

class HelpCenterController extends Controller
{
    private IEncryptionService $encryptionService;
    private IHelpCenterRepository $helpCenterRepository;
    
    public function __construct(IHelpCenterRepository $helpCenterRepository,
                                IEncryptionService $encryptionService)
    {
        $this->helpCenterRepository = $helpCenterRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $records = $this->helpCenterRepository->getAll();

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
     * @param HelpCenterRequest $request
     * @return JsonResponse
     */
    public function store(HelpCenterRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $createRecord = $this->helpCenterRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param HelpCenter $helpCenter
     * @return JsonResponse
     */
    public function show(HelpCenter $help_center): JsonResponse {
        $encryptedResponse = $this->encryptionService->encrypt($help_center->toArray());
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
     * @param HelpCenter $helpCenter
     * @param HelpCenterRequest $request
     * @return JsonResponse
     */
    public function update(HelpCenterRequest $request, HelpCenter $help_center): JsonResponse {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $updateRecord = $this->helpCenterRepository->update($help_center, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(HelpCenter $help_center): JsonResponse
    {
        $deleteRecord = $this->helpCenterRepository->delete($help_center);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function inputBody(array $details): array {
        $body = array(
                    'title'=>$details['title'],
                    'description'=>$details['description'],
                    'image_location'=>$details['image_location'],
                    'order'=>$details['order'],
                );
        return $body;
    }
}
