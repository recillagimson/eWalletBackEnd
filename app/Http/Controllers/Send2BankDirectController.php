<?php

namespace App\Http\Controllers;

use App\Services\Utilities\Responses\IResponseService;
use App\Services\Send2BankDirect\ISend2BankDirectService;

class Send2BankDirectController extends Controller
{
    private IResponseService $responseService;
    private ISend2BankDirectService $send2BankDirect;

    public function __construct(IResponseService $responseService, ISend2BankDirectService $send2BankDirect)
    {
        $this->responseService = $responseService;
        $this->send2BankDirect = $send2BankDirect;
    }

    public function send2BankDirect() {
        $this->send2BankDirect->send2BankDirect();
    }


}
