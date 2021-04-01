<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use App\Http\Requests\SendMoney\CheckBalanceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\SendMoney\ISendMoneyService;
use App\Enums\UsernameTypes;

class SendMoneyController extends Controller
{       
    private ISendMoneyService $sendMoneyService;

    public function __construct(ISendMoneyService $sendMoneyService)
    {   
        $this->sendMoneyService = $sendMoneyService;
    }

    /**
     * Send money request
     *
     * 
     * @param SendMoneyRequest $request
     * @param array $fillRequest
     * @param string $username
     * @param string $senderID
     * @param string $receiverID
     * @param boolean $isSelf
     * @param boolean $isEnough
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendMoney(SendMoneyRequest $request): JsonResponse
    {       
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
        $senderID = '3f94e2a7-f3f0-40c8-a88f-3027fcdc7135';
        $receiverID = $this->sendMoneyService->getUserID($username, $fillRequest);
        $isSelf = $this->sendMoneyService->isSelf($senderID, $receiverID);
        $isEnough = $this->sendMoneyService->validateAmount($senderID, $fillRequest);

        if($isSelf){ $this->sendMoneyService->errorMessage('receiver','not allowed to send money to your account'); }
        if(!$isEnough){ $this->sendMoneyService->errorMessage('amount', 'not enough balance'); }

        $senderNewBalance = $this->sendMoneyService->subtractSenderBalance($senderID, $fillRequest);
        $receiverNewBalance = $this->sendMoneyService->addReceiverBalance($receiverID, $fillRequest);

        return response()->json(['Sender Balance | ' . $senderNewBalance,'Receiver Balance | ' . $receiverNewBalance], Response::HTTP_OK);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }




}
