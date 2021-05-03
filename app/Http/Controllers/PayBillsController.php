<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBills\PayBillsRequest;
use App\Services\PayBills\IPayBillsService;

class PayBillsController extends Controller
{
    private IPayBillsService $payBillsService;

    public function __construct(IPayBillsService $payBillsService)
    {
        $this->payBillsService = $payBillsService;
    }
    
    public function getBillers(){
        return $this->payBillsService->getBillers('services');
    }

    public function createPayment(PayBillsRequest $request){
        $fillRequest = $request->validated();
        return $this->payBillsService->createPayment($request->user());
    }

}
