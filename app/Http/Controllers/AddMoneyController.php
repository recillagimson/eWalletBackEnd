<?php

namespace App\Http\Controllers;

use App\Http\Requests\DragonPay\AddMoneyCancelRequest;
use App\Http\Requests\DragonPay\AddMoneyRequest;
use App\Http\Requests\DragonPay\DragonPayPostBackRequest;
use App\Services\AddMoney\DragonPay\IHandlePostBackService;
use App\Services\AddMoney\IInAddMoneyService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AddMoneyController extends Controller
{
    private IHandlePostBackService $postBackService;
    private IEncryptionService $encryptionService;
    private IInAddMoneyService $addMoneyService;

    public function __construct(IHandlePostBackService $postBackService,
                                IEncryptionService $encryptionService,
                                IInAddMoneyService $addMoneyService) {

        $this->postBackService = $postBackService;
        $this->encryptionService = $encryptionService;
        $this->addMoneyService = $addMoneyService;
    }

    public function addMoney(AddMoneyRequest $request)
    {
        $requestParams = $request->validated();
        $user = $request->user();

        $addMoney = $this->addMoneyService->addMoney($user, $requestParams);
        $encryptedResponse = $this->encryptionService->encrypt(array($addMoney));

        return response()->json($addMoney, Response::HTTP_OK);
    }

    public function postBack(DragonPayPostBackRequest $request)
    {
        $postBackData = $request->validated();

        $postBack = $this->postBackService->insertPostBackData($postBackData);
        $encryptedResponse = $this->encryptionService->encrypt(array($postBack));

        return response()->json($postBack, Response::HTTP_OK);
    }

    public function cancel(AddMoneyCancelRequest $request)
    {
        $referenceNumber = $request->validated();
        $user = $request->user();

        $cancel = $this->addMoneyService->cancelAddMoney($user, $referenceNumber);
        $encryptedResponse = $this->encryptionService->encrypt(array($cancel));

        return response()->json($encryptedResponse, Response::HTTP_OK);
    }
}
