<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PrepaidLoad\PrepaidLoadRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\IOutBuyLoadService;

class PrepaidLoadController extends Controller
{
    private IEncryptionService $encryptionService;
    private IOutBuyLoadService $outBuyLoadService;


    public function __construct(IEncryptionService $encryptionService, 
                                IOutBuyLoadService $outBuyLoadService)
    {
        $this->encryptionService = $encryptionService;
        $this->outBuyLoadService = $outBuyLoadService;
    }


     /**
     * Load
     *
     * @param PrepaidLoadRequest $request
     * @return JsonResponse
     */
    public function load(Request $request, PrepaidLoadRequest $prepaidloadrequest): JsonResponse
    {
        $details = $prepaidloadrequest->validated();
        $load = $this->outBuyLoadService->load($details);
        // on fail
        $createRecord = $this->outBuyLoadService->createRecord($details, $request);
        $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Show list of promos
     *
     * @return JsonResponse
     */
    public function showPromos(): JsonResponse {
        $getNetworkPromos = $this->outBuyLoadService->showNetworkPromos();
        $encryptedResponse = $this->encryptionService->encrypt($getNetworkPromos);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
