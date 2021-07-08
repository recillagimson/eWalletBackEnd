<?php

namespace App\Http\Controllers\SecurityBank;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\SecurityBank\IPesoNetBankRepository;

class PesoNetController extends Controller
{

    private IResponseService $responseService;
    private IPesoNetBankRepository $pesoNetBank;

    public function __construct(IResponseService $responseService, IPesoNetBankRepository $pesoNetBank)
    {
        $this->responseService = $responseService;
        $this->pesoNetBank = $pesoNetBank;
    }

    public function getBanks() {
        $records = $this->pesoNetBank->getListSorted('bank_name', 'ASC');
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }
}
