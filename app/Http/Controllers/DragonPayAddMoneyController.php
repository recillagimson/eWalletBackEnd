<?php

namespace App\Http\Controllers;

use App\Http\Requests\DragonPay\AddMoneyCancelRequest;
use App\Http\Requests\DragonPay\AddMoneyStatusRequest;
use App\Http\Requests\DragonPay\AddMoneyWebBankRequest;
use App\Http\Requests\DragonPay\DragonPayPostBackRequest;
use App\Services\AddMoney\DragonPay\IWebBankingService;
use App\Services\AddMoney\DragonPay\PostBack\IHandlePostBackService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DragonPayAddMoneyController extends Controller
{
    private IWebBankingService $webBankingService;
    private IHandlePostBackService $postBackService;
    private IEncryptionService $encryptionService;

    public function __construct(IWebBankingService $webBankingService,
                                IHandlePostBackService $postBackService,
                                IEncryptionService $encryptionService) {

        $this->webBankingService = $webBankingService;
        $this->postBackService = $postBackService;
        $this->encryptionService = $encryptionService;
    }

    public function addMoney(AddMoneyWebBankRequest $request)
    {
        $urlParams = $request->validated();
        $user = $request->user();

        $requestURL = $this->webBankingService->generateRequestURL($user, $urlParams);
        $encryptedResponse = $this->encryptionService->encrypt(array($requestURL));
        
        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    public function postBack(DragonPayPostBackRequest $request)
    {
        $postBackData = $request->validated();

        $postBack = $this->postBackService->insertPostBackData($postBackData);
        $encryptedResponse = $this->encryptionService->encrypt(array($postBack));

        return response()->json($encryptedResponse, Response::HTTP_OK);
    }

    public function cancel(AddMoneyCancelRequest $request)
    {
        $referenceNumber = $request->validated();
        $user = $request->user();

        $cancel = $this->webBankingService->cancelAddMoney($user, $referenceNumber);
        $encryptedResponse = $this->encryptionService->encrypt(array($cancel));

        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
