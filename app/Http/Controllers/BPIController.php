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
}
