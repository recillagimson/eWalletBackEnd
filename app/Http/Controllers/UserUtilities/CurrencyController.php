<?php

namespace App\Http\Controllers\UserUtilities;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserUtilities\CurrencyRequest;
use App\Models\UserUtilities\Currency;
use App\Repositories\UserUtilities\Currency\ICurrencyRepository;
use App\Services\Encryption\IEncryptionService;
use App\Services\UserProfile\IUserProfileService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{

    private IEncryptionService $encryptionService;
    private ICurrencyRepository $currencyRepository;
    private IUserProfileService $userProfileService;
    private IResponseService $responseService;

    public function __construct(ICurrencyRepository $currencyRepository,
                                IEncryptionService  $encryptionService,
                                IUserProfileService $userProfileService,
                                IResponseService    $responseService)
    {
        $this->currencyRepository = $currencyRepository;
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
        $records = $this->currencyRepository->getAll()->sortBy('description');

        // $encryptedResponse = $this->encryptionService->encrypt($records->toArray());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
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
        $inputBody = $this->userProfileService->addUserInput($details, $request->user());
        $createRecord = $this->currencyRepository->create($inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt($createRecord->toArray());
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::recordSaved);
    }

    /**
     * Display the specified resource.
     *
     * @param  Currency $currency
     * @return JsonResponse
     */
    public function show(Currency $currency): JsonResponse
    {
        // $encryptedResponse = $this->encryptionService->encrypt($currency->toArray());
        return $this->responseService->successResponse($currency->toArray(), SuccessMessages::success);
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
        $inputBody = $this->userProfileService->addUserInput($details, $request->user(), $currency);
        $updateRecord = $this->currencyRepository->update($currency, $inputBody);

        // $encryptedResponse = $this->encryptionService->encrypt(array($updateRecord));
        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::recordSaved);
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

        return $this->responseService->successResponse(null, SuccessMessages::recordDeleted);
    }
}
