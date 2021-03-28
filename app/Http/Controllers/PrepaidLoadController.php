<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PrepaidLoad\GlobeRequest;
use App\Services\PrepaidLoad\IPrepaidLoadService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;

class PrepaidLoadController extends Controller
{
    private IPrepaidLoadService $prepaidLoadService;
    private IEncryptionService $encryptionService;


    public function __construct(IPrepaidLoadService $prepaidLoadService, IEncryptionService $encryptionService)
    {
        $this->prepaidLoadService = $prepaidLoadService;
        $this->encryptionService = $encryptionService;
    }


     /**
     * Authenticate a user
     *
     * @param GlobeRequest $request
     * @return JsonResponse
     */
    public function loadGlobe(GlobeRequest $request): JsonResponse
    {
        $details = $request->validated();
        $loadGlobe = $this->prepaidLoadService->loadGlobe($details);
        $encryptedResponse = $this->encryptionService->encrypt($loadGlobe);
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
        
        // return response()->json($this->prepaidLoadService->loadGlobe($details), Response::HTTP_OK);
    }

    public function showGlobePromos() {
        $getAllGlobePromos = $this->prepaidLoadService->prepaidLoads->getAll();
        $encryptedResponse = $this->encryptionService->encrypt($getAllGlobePromos->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
