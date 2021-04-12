<?php

namespace App\Http\Controllers\UserUtilities;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserUtilities\Currency\ICurrencyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\UserUtilities\CurrencyRequest;
use App\Models\UserUtilities\Currency;

class CurrencyController extends Controller
{

    private IEncryptionService $encryptionService;
    private ICurrencyRepository $currencyRepository;
    
    public function __construct(ICurrencyRepository $currencyRepository,
                                IEncryptionService $encryptionService)
    {
        $this->currencyRepository = $currencyRepository;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $records = $this->currencyRepository->getAll();

        $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CurrencyRequest $request
     * @return JsonResponse
     */
    public function store(CurrencyRequest $request): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $createRecord = $this->currencyRepository->create($inputBody);

        $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Currency $currency
     * @return JsonResponse
     */
    public function show(Currency $currency): JsonResponse
    {
        $encryptedResponse = $this->encryptionService->encrypt($currency->toArray());
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CurrencyRequest $request
     * @param  Currency $currency
     * @return JsonResponse
     */
    public function update(CurrencyRequest $request, Currency $currency): JsonResponse
    {
        $details = $request->validated();
        $inputBody = $this->inputBody($details, $request->user()->id);
        $updateRecord = $this->currencyRepository->update($currency, $inputBody);

        $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Currency $currency
     * @return JsonResponse
     */
    public function destroy(Currency $currency): JsonResponse
    {
        $deleteRecord = $this->currencyRepository->delete($currency);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function inputBody(array $details, string $user_id): array {
        $body = array(
                    'description'=>$details['description'],
                    'code'=>$details['code'],
                    'status'=>$details['status'],
                    'user_created'=>$user_id,
                );
        return $body;
    }
}
