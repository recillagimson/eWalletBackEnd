<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DragonPay\AddMoneyCancelRequest;
use App\Http\Requests\DragonPay\AddMoneyRequest;
use App\Http\Requests\DragonPay\AddMoneyStatusRequest;
use App\Http\Requests\DragonPay\DragonPayPostBackRequest;
use App\Repositories\InAddMoney\IInAddMoneyRepository;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AddMoneyController extends Controller
{
    private IHandlePostBackService $postBackService;
    private IInAddMoneyService $addMoneyService;
    private IResponseService $responseService;
    private IInAddMoneyRepository $addMoneys;

    public function __construct(IHandlePostBackService $postBackService,
                                IEncryptionService $encryptionService,
                                IInAddMoneyService $addMoneyService,
                                IResponseService $responseService,
                                IInAddMoneyRepository $addMoneys) {

        $this->postBackService = $postBackService;
        $this->encryptionService = $encryptionService;
        $this->addMoneyService = $addMoneyService;
        $this->responseService = $responseService;
        $this->addMoneys = $addMoneys;
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

    public function getStatus(AddMoneyStatusRequest $request)
    {
        $user = $request->user();
        $requestParams = $request->validated();

        $status = $this->addMoneyService->getStatus($user, $requestParams);

        return $this->responseService->successResponse($status, SuccessMessages::addMoneyStatusAcquired);
    }

    public function getLatestPendingTrans(Request $request)
    {
        $user = $request->user();

        $transaction = $this->addMoneys->getLatestPendingByUserAccountID($user->id);

        return $this->responseService->successResponse($transaction->toArray(), SuccessMessages::success);
    }

    public function updateUserTrans(Request $request)
    {
        $user = $request->user();

        $updatedTransactions = $this->addMoneyService->updateUserTransactionStatus($user);

        return $this->responseService->successResponse($updatedTransactions, SuccessMessages::success);
    }
}
