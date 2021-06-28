<?php

namespace App\Http\Controllers;

use App\Services\BPIService\IBPIService;
use Illuminate\Http\Request;

class BPIController extends Controller
{

    private IBPIService $bpiService;

    public function __construct(IBPIService $bpiService) 
    {
        $this->bpiService = $bpiService;   
    }

    public function getAccounts(Request $request) {
        return $this->bpiService->getAccounts($request->token);
    }

    public function fundTopUp(Request $request) {
        $data = [
            'accountNumberToken' => $request->accountNumberToken,
            'amount' => $request->amount,
            'remarks' => $request->remarks
        ];
        return $this->bpiService->fundTopUp($data, $request->token);
    }
}
