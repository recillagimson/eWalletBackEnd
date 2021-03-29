<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PrepaidLoad\GlobeRequest;
use App\Services\PrepaidLoad\IPrepaidLoadService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Encryption\IEncryptionService;
use App\Services\OutBuyLoad\IOutBuyLoadService;

class PrepaidLoadController extends Controller
{
    private IPrepaidLoadService $prepaidLoadService;
    private IEncryptionService $encryptionService;
    private IOutBuyLoadService $outBuyLoadService;


    public function __construct(IPrepaidLoadService $prepaidLoadService, IEncryptionService $encryptionService, IOutBuyLoadService $outBuyLoadService)
    {
        $this->prepaidLoadService = $prepaidLoadService;
        $this->encryptionService = $encryptionService;
        $this->outBuyLoadService = $outBuyLoadService;
    }


     /**
     * Load Globe
     *
     * @param GlobeRequest $request
     * @return JsonResponse
     */
    public function loadGlobe(GlobeRequest $request): JsonResponse
    {
        $details = $request->validated();
        $loadGlobe = $this->prepaidLoadService->loadGlobe($details);
        // on fail
        $getPromoDetails = $this->outBuyLoadService->outBuyLoads->getByRewardKeyword($details['promo']);
        $inputOutBuyLoad = $this->inputOutBuyLoad($getPromoDetails);
        $createOutBuyLoad = $this->prepaidLoadService->prepaidLoads->create($inputOutBuyLoad);
        $encryptedResponse = $this->encryptionService->encrypt($loadGlobe);
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Show list of Globe promos
     *
     * @return JsonResponse
     */
    public function showGlobePromos(): JsonResponse {
        $getAllGlobePromos = $this->prepaidLoadService->prepaidLoads->getAll();
        $encryptedResponse = $this->encryptionService->encrypt($getAllGlobePromos->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * input body array
     * @param array $promos
     * @return object
     */
    private function inputOutBuyLoad(array $promos): object {
        $body = array(
                    'user_account_id'=>'',
                    'prepaid_load_id'=>$promos->id,
                    'total_amount'=>$promos->amount,
                    // 'transaction_date'=>'',
                    // 'transaction_category_id',
                    'transaction_remarks'=>'',
                );
        return $body;
    }
}
