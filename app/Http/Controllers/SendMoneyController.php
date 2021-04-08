<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\SendMoney\ISendMoneyService;
use App\Enums\UsernameTypes;
use App\Services\Encryption\IEncryptionService;
use App\Http\Requests\SendMoney\SendMoneyQrRequest;

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
     * @param string $encryptedResponse
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendMoney(SendMoneyRequest $request):JsonResponse
    {
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
        $senderID = $request->user()->id;
        $receiverID = $this->sendMoneyService->getUserID($username, $fillRequest);
        
        $isSelf = $this->sendMoneyService->isSelf($senderID, $receiverID);
        $isEnough = $this->sendMoneyService->checkAmount($senderID, $fillRequest);
        $fillRequest['refNo'] = $this->sendMoneyService->generateRefNo();

        if($isSelf){ $this->sendMoneyService->errorMessage($username,'Can\'t send to your own account'); }
        if(!$isEnough){ $this->sendMoneyService->errorMessage('amount', 'Not enough balance'); }
        
        $this->sendMoneyService->subtractSenderBalance($senderID, $fillRequest);
        $this->sendMoneyService->addReceiverBalance($receiverID, $fillRequest);
        $this->sendMoneyService->outSendMoney($senderID, $receiverID, $fillRequest);
        $this->sendMoneyService->inReceiveMoney($senderID, $receiverID, $fillRequest);

        $encryptedResponse = $this->encryptionService->encrypt([ "status" => "success"]);
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }


    /**
     * Generate QR Transaction
     *
     * @param SendMoneyRequest $request
     * @param array $fillRequest
     * @param array $qrTransaction
     * @param string $encryptedResponse
     * @return JsonResponse
     */
    public function generateqr(SendMoneyQrRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $qrTransaction = $this->sendMoneyService->createUserQR($request->user(), $fillRequest);
        $encryptedResponse = $this->encryptionService->encrypt([
            'user_account_id' => $qrTransaction->user_account_id,
            'amount' => $qrTransaction->amount
        ]);

        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }


    /**
     * Returning UsernameTypes
     * 
     * @param Request $request
     * @return string
     */
    private function getUsernameField(Request $request): string
    {
        return $request->has(UsernameTypes::Email) ? UsernameTypes::Email : UsernameTypes::MobileNumber;
    }
    



    

}


