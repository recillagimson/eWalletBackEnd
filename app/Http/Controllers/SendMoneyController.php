<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
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
        $senderID = '958570ab-ac74-4056-a9c5-bd433cc0bf87';
        $fillRequest = $request->validated();
        $username = $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
        $receiverID = $this->sendMoneyService->getUserID($username, $fillRequest);
        $isEnough = $this->sendMoneyService->validateAmount($receiverID, $fillRequest);
        $senderNewBalance = $this->sendMoneyService->subtractSenderBalance($senderID, $receiverID, $fillRequest);
        
        return response()->json($senderNewBalance, Response::HTTP_OK);
        
        if(!$isEnough){
            $this->sendMoneyService->notEnoughBalance();
        }

        return response()->json(['successfully transfered money!'], Response::HTTP_OK);
    }




}
