<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use Illuminate\Http\Request;
use App\Http\Requests\SendMoney\SendMoneyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\SendMoney\ISendMoneyService;
use App\Enums\UsernameTypes;
use App\Http\Requests\SendMoney\GenerateQrRequest;
use App\Http\Requests\SendMoney\ScanQrRequest;
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
     */
     public function send(SendMoneyRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $username = $this->getUsernameField($request);
         $this->sendMoneyService->send($username, $fillRequest, $request->user());

        $response = [
            'message' => SuccessMessages::sendMoneySuccessFul,
            'data' => $this->encryptionService->encrypt(['status' => 'success'])
        ];
        return response()->json($response, Response::HTTP_CREATED);
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

        $response = [
            'message' => SuccessMessages::generateQrSuccessful,
            'data' =>  $this->encryptionService->encrypt(['id' => $qrTransaction->id])
        ];
       
        return response()->json($response, Response::HTTP_CREATED);
    }


    /**
     * Scan Qr Transaction
     *
     * @param ScanQrRequest $request
     * @param array $fillRequest
     * @param array $qrTransaction
     * @param string $encryptedResponse
     * @return JsonResponse
     */
    public function scanQr(ScanQrRequest $request)//: JsonResponse
    {
        $fillRequest = $request->validated();
        $qrTransaction = $this->sendMoneyService->scanQr($fillRequest['id']);
        $response = [
            'message' => SuccessMessages::scanQrSuccessful,
            'data' =>  $this->encryptionService->encrypt($qrTransaction)
        ];

        return response()->json($response, Response::HTTP_CREATED);
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


