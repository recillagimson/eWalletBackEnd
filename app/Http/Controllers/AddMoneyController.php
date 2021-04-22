<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DragonPay\AddMoneyCancelRequest;
use App\Http\Requests\DragonPay\AddMoneyRequest;
use App\Http\Requests\DragonPay\DragonPayPostBackRequest;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AddMoneyController extends Controller
{
    private IHandlePostBackService $postBackService;
    private IEncryptionService $encryptionService;
    private IInAddMoneyService $addMoneyService;
    private IResponseService $responseService;

    public function __construct(IHandlePostBackService $postBackService,
                                IEncryptionService $encryptionService,
                                IInAddMoneyService $addMoneyService,
                                IResponseService $responseService) {

        $this->postBackService = $postBackService;
        $this->encryptionService = $encryptionService;
        $this->addMoneyService = $addMoneyService;
        $this->responseService = $responseService;
    }

    public function addMoney(AddMoneyRequest $request)
    {
        $requestParams = $request->validated();
        $user = $request->user();

        $addMoney = $this->addMoneyService->addMoney($user, $requestParams);

        return $this->responseService->successResponse($addMoney, SuccessMessages::URLGenerated);
    }

    public function postBack(DragonPayPostBackRequest $request)
    {
        $postBackData = $request->validated();

        $postBack = $this->postBackService->insertPostBackData($postBackData);

        return $postBack;
        // return response() is handled in the service for complication reasons
    }

    public function cancel(AddMoneyCancelRequest $request)
    {
        $referenceNumber = $request->validated();
        $user = $request->user();

        $cancel = $this->addMoneyService->cancelAddMoney($user, $referenceNumber);

        return $this->responseService->successResponse(null, SuccessMessages::addMoneyCancel);
    }
}
