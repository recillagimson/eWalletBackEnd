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
use App\Services\AddMoneyV2\IAddMoneyService;
use App\Services\Encryption\IEncryptionService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ThirdParty\ECPay\IECPayService;
use App\Http\Requests\EcPayRequest\CommitPaymentRequest;
use App\Http\Requests\EcPayRequest\ConfirmPaymentRequest;

class AddMoneyController extends Controller
{
    private IHandlePostBackService $postBackService;
    private IInAddMoneyService $addMoneyService;
    private IResponseService $responseService;
    private IInAddMoneyRepository $addMoneys;
    private IAddMoneyService $addMoneyServiceV2;
    private IECPayService $ecpayService;

    public function __construct(IHandlePostBackService $postBackService,
                                IEncryptionService $encryptionService,
                                IInAddMoneyService $addMoneyService,
                                IResponseService $responseService,
                                IInAddMoneyRepository $addMoneys,
                                IAddMoneyService $addMoneyServiceV2,
                                IECPayService $ecpayService)
    {

        $this->postBackService = $postBackService;
        $this->encryptionService = $encryptionService;
        $this->addMoneyService = $addMoneyService;
        $this->responseService = $responseService;
        $this->addMoneys = $addMoneys;
        $this->addMoneyServiceV2 = $addMoneyServiceV2;
        $this->ecpayService = $ecpayService;
    }

    public function addMoney(AddMoneyRequest $request): JsonResponse
    {
        $requestParams = $request->validated();
        $user = $request->user();

        $addMoney = $this->addMoneyServiceV2->generateUrl($user->id, $requestParams);
        return $this->responseService->successResponse($addMoney, SuccessMessages::URLGenerated);
    }

    public function postBack(DragonPayPostBackRequest $request)
    {
        $postBackData = $request->validated();
        $this->addMoneyServiceV2->handlePostBack($postBackData);

        return response('', 200, [
            'Content-type' => 'text/plain'
        ]);
    }

    public function cancel(AddMoneyCancelRequest $request): JsonResponse
    {
        $referenceNumber = $request->validated();
        $user = $request->user();

        $this->addMoneyService->cancelAddMoney($user, $referenceNumber);
        return $this->responseService->successResponse(null, SuccessMessages::addMoneyCancel);
    }

    public function getStatus(AddMoneyStatusRequest $request): JsonResponse
    {
        $user = $request->user();
        $requestParams = $request->validated();

        $status = $this->addMoneyService->getStatus($user, $requestParams);

        return $this->responseService->successResponse($status, SuccessMessages::addMoneyStatusAcquired);
    }

    public function getLatestPendingTrans(Request $request): JsonResponse
    {
        $user = $request->user();

        $transaction = $this->addMoneys->getLatestPendingByUserAccountID($user->id);

        return $this->responseService->successResponse($transaction->toArray(), SuccessMessages::success);
    }

    public function updateUserTrans(Request $request): JsonResponse
    {
        $user = $request->user();
        $updatedTransactions = $this->addMoneyServiceV2->processPending($user->id);
        return $this->responseService->successResponse($updatedTransactions, SuccessMessages::success);
    }

    public function commitPayment(CommitPaymentRequest $request): JsonResponse {

        $data = $request->validated();
        return $this->ecpayService->commitPayment($data, $request->user());
    }

    public function confirmPayment(ConfirmPaymentRequest $request): JsonResponse {

        $data = $request->validated();
        return $this->ecpayService->confirmPayment($data, $request->user());
    }
}
