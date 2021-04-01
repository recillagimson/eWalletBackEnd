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
     * Show List
     *
     * 
     * @return JsonResponse
     */
    public function GetAll(): JsonResponse {
        $records = $this->helpCenterRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Create Record
     *
     * @param HelpCenterRequest $request
     * @return JsonResponse
     */
    public function create(HelpCenterRequest $request): JsonResponse {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $createRecord = $this->helpCenterRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Show Record
     *
     * @param HelpCenter $helpCenter
     * @return JsonResponse
     */
    public function show(HelpCenter $helpCenter): JsonResponse {
        $encryptedResponse = $this->encryptionService->encrypt($helpCenter->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update Record
     *
     * @param HelpCenter $helpCenter
     * @param HelpCenterRequest $request
     * @return JsonResponse
     */
    public function update(HelpCenter $helpCenter, HelpCenterRequest $request): JsonResponse {
        $details = $request->validated();
        $inputBody = $this->inputBody($details);
        $updateRecord = $this->helpCenterRepository->update($helpCenter, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Delete Record
     *
     * @param string $id
     * @return JsonResponse
     */
    public function delete(HelpCenter $helpCenter): JsonResponse {
        $deleteRecord = $this->helpCenterRepository->delete($helpCenter);

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
