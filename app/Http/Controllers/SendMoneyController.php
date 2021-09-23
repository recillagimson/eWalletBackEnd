<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Enums\UsernameTypes;
use App\Http\Requests\SendMoney\GenerateQrRequest;
use App\Http\Requests\SendMoney\ScanQrRequest;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use App\Repositories\UserUtilities\UserDetail\IUserDetailRepository;
use App\Services\SendMoney\ISendMoneyService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendMoneyController extends Controller
{
    private ISendMoneyService $sendMoneyService;
    private IResponseService $responseService;
    private IUserDetailRepository $userDetail;

    public function __construct(ISendMoneyService $sendMoneyService, IResponseService $responseService, IUserDetailRepository $userDetail)
    {
        $this->sendMoneyService = $sendMoneyService;
        $this->responseService = $responseService;
        $this->userDetail = $userDetail;
    }


    /**
     * Send money request
     *
     * @param SendMoneyRequest $request
     * @return JsonResponse
     */
    public function send(SendMoneyRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $postback = $this->sendMoneyService->send($usernameField, $fillRequest, $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::sendMoneySuccessFul);
    }

    /**
     * Validates send money
     *
     * @param SendMoneyRequest $request
     * @return JsonResponse
     */
    public function sendValidate(SendMoneyRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $usernameField = $this->getUsernameField($request);
        $review = $this->sendMoneyService->sendValidate($usernameField, $fillRequest, $request->user());
        $userDetail = $this->userDetail->getByUserId($review['user_account_id']);
        $data = array_merge($fillRequest,$review);
        if($userDetail) {
            $data['avatar_link'] = $userDetail->avatar_link;
        }
        return $this->responseService->successResponse($data, SuccessMessages::validateSendMoney);
    }



    /**
     * Generates QR Transaction
     *
     * @param GenerateQrRequest $request
     * @return JsonResponse
     */
    public function generateQr(GenerateQrRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $qrTransaction = $this->sendMoneyService->generateQR($request->user(), $fillRequest);

        return $this->responseService->createdResponse(['id' => $qrTransaction->id], SuccessMessages::generateQrSuccessful);
    }


    /**
     * Scan Qr Transaction
     *
     * @param ScanQrRequest $request
     * @return JsonResponse
     */
    public function scanQr(ScanQrRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $qrTransaction = $this->sendMoneyService->scanQr($fillRequest['id']);

        return $this->responseService->successResponse($qrTransaction, SuccessMessages::scanQrSuccessful);
    }

    /**
     * Returns UsernameType
     *
     * @param Request $request
     * @return string
     */
    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }


}

