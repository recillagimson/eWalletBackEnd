<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\SendMoney\ISendMoneyService;
use App\Enums\UsernameTypes;
use App\Http\Requests\SendMoney\GenerateQrRequest;
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
     * @param string $encryptedResponse
     * @return JsonResponse
     * @throws ValidationException
     */
     public function send(SendMoneyRequest $request)
    {
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
        $this->sendMoneyService->send($username, $fillRequest, $request->user());

        $encryptedResponse = $this->encryptionService->encrypt([ "status" => "success"]);
        return response()->json($encryptedResponse, Response::HTTP_CREATED);
    }



    /**
     * Generates QR Transaction
     *
     * @param SendMoneyRequest $request
     * @param array $fillRequest
     * @param array $qrTransaction
     * @param string $encryptedResponse
     * @return JsonResponse
     */
    public function generateQr(GenerateQrRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $qrTransaction = $this->sendMoneyService->generateQR($request->user(), $fillRequest);
        $encryptedResponse = $this->encryptionService->encrypt([
            'user_account_id' => $qrTransaction->user_account_id,
            'amount' => $qrTransaction->amount,
            'message' => ''
        ]);

        return response()->json($encryptedResponse, Response::HTTP_CREATED);
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


