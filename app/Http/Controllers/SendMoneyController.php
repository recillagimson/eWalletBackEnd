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
     * Display something.
     *
     * @param SendMoneyRequest $request
     * @return JsonResponse
     */
    public function sendMoney(SendMoneyRequest $request): JsonResponse
    {       
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
        $senderID = '7d1b8829-92e2-4647-8ba5-92d0dc0fb1f2';
        $receiverID = $this->sendMoneyService->getUserID($username, $fillRequest);
        $isSelf = $this->sendMoneyService->isSelf($senderID, $receiverID);
        $isEnough = $this->sendMoneyService->validateAmount($senderID, $fillRequest);

        if($isSelf){ $this->sendMoneyService->errorMessage('not allowed to send money to your account'); }
        if(!$isEnough){ $this->sendMoneyService->errorMessage('not enough balance'); }

        $senderNewBalance = $this->sendMoneyService->subtractSenderBalance($senderID, $fillRequest);
        $receiverNewBalance = $this->sendMoneyService->addReceiverBalance($receiverID, $fillRequest);

        return response()->json(['Sender Balance | ' . $senderNewBalance,'Receiver Balance | ' . $receiverNewBalance], Response::HTTP_OK);
    }

    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }




}
