<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\SendMoney\ISendMoneyService;
use App\Enums\UsernameTypes;
use App\Services\Encryption\IEncryptionService;

class SendMoneyController extends Controller
{       
    private ISendMoneyService $sendMoneyService;
    private IEncryptionService $encryptionService;

    public function __construct(ISendMoneyService $sendMoneyService, IEncryptionService $encryptionService)
    {   
        $this->sendMoneyService = $sendMoneyService;
        $this->encryptionService = $encryptionService;
    }

    /**
     * Send money request
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
    public function sendMoney(SendMoneyRequest $request)
    {
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
        $senderID = $fillRequest['user_account_id'];
        $receiverID = $this->sendMoneyService->getUserID($username, $fillRequest);
        
        $isSelf = $this->sendMoneyService->isSelf($senderID, $receiverID);
        $isEnough = $this->sendMoneyService->validateAmount($senderID, $fillRequest);
        $fillRequest['refNo'] = $this->sendMoneyService->generateRefNo();

        if($isSelf){ $this->sendMoneyService->errorMessage('receiver','not allowed to send money to your account'); }
        if(!$isEnough){ $this->sendMoneyService->errorMessage('amount', 'not enough balance'); }

        $this->sendMoneyService->subtractSenderBalance($senderID, $fillRequest);
        $this->sendMoneyService->addReceiverBalance($receiverID, $fillRequest);
        $outSendMoney = $this->sendMoneyService->outSendMoney($senderID, $receiverID, $fillRequest);
        $inReceiveMoney = $this->sendMoneyService->inReceiveMoney($senderID, $receiverID, $fillRequest);

        $encryptedResponseOutSendMoney = $this->encryptionService->encrypt($outSendMoney->toArray());
        $encryptedResponseInReceiveMoney = $this->encryptionService->encrypt($inReceiveMoney->toArray());
        return response()->json([$encryptedResponseOutSendMoney, $encryptedResponseInReceiveMoney], Response::HTTP_CREATED);
    }


    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }




}
