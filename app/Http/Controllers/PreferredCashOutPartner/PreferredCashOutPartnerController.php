<?php

namespace App\Http\Controllers\PreferredCashOutPartner;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\PreferredCashOutPartner\PreferredCashOutPartnerRequest;
use App\Repositories\PreferredCashOutPartner\IPreferredCashOutPartnerRepository;

class PreferredCashOutPartnerController extends Controller
{
    private IPreferredCashOutPartnerRepository $preferredCashOutPartner;
    private IResponseService $responseService;
    public function __construct(IPreferredCashOutPartnerRepository $preferredCashOutPartner, IResponseService $responseService)
    {
        $this->preferredCashOutPartner = $preferredCashOutPartner;
        $this->responseService = $responseService;
    }

    public function list() {
        $records = $this->preferredCashOutPartner->getAll();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function store(PreferredCashOutPartnerRequest $request) {
        $record = $this->preferredCashOutPartner->create([
            'description' => $request->description,
            'user_created' => request()->user()->id,
            'user_updated' => request()->user()->id,
            'status' => 'APPROVED'
        ]);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }
}
