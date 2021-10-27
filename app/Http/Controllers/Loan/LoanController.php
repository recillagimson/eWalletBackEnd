<?php

namespace App\Http\Controllers\Loan;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Services\Loan\ILoanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\StoreReferenceNumberRequest;
use App\Services\Utilities\Responses\IResponseService;

class LoanController extends Controller
{
    private ILoanService $loanService;
    private IResponseService $responseService;

    public function __construct(ILoanService $loanService, IResponseService $responseService)
    {
        $this->loanService = $loanService;
        $this->responseService = $responseService;
    }

    public function generateReferenceNumber() {
        $record = $this->loanService->generateReferenceNumber();
        return $this->responseService->successResponse(['reference_number' => $record], SuccessMessages::success);
    }

    public function storeReferenceNumber(StoreReferenceNumberRequest $request) {
        $record = $this->loanService->storeReferenceNumber($request->all());
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }
}
