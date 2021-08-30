<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Repositories\InAddMoneyUpbDirect\IInAddMoneyUpbDirectRepository;

class InAddMoneyUpbDirectController extends Controller
{
    //
    private IInAddMoneyUpbDirectRepository $inAddMoneyUpbDirectRepository;

    public function __construct(IInAddMoneyUpbDirectRepository $inAddMoneyUpbDirectRepository)
    {
        $this->inAddMoneyUpbDirectRepository = $inAddMoneyUpbDirectRepository;
    }

    public function addMoney(Request $request)
    {
        $data = $request->all();
        $data['state'] = json_decode($data['state'], true);
        $response = $this->inAddMoneyUpbDirectRepository->addMoney($data);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }
}
