<?php

namespace App\Http\Controllers\SecurityBank;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\SecurityBank\PesoNetRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\SecurityBank\IPesoNetBankRepository;
use App\Services\Send2BankSecBank\IPesoNetService;

class PesoNetController extends Controller
{

    private IResponseService $responseService;
    private IPesoNetBankRepository $pesoNetBankRepo;
    private IPesoNetService $pesoNetService;

    public function __construct(IResponseService $responseService, IPesoNetBankRepository $pesoNetBankRepo, IPesoNetService $pesoNetService)
    {
        $this->responseService = $responseService;
        $this->pesoNetBankRepo = $pesoNetBankRepo;
        $this->pesoNetService = $pesoNetService;
    }

    public function getBanks() {
        $records = $this->pesoNetBankRepo->getListSorted('bank_name', 'ASC');
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function validateTransaction(PesoNetRequest $request) {
        $response = $this->pesoNetService->validateTransaction($request->all(), request()->user()->id);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function transfer(PesoNetRequest $request) {
        $response = $this->pesoNetService->transfer($request->all(), request()->user()->id);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }
}
