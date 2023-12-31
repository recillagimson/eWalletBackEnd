<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\Cebuana\CebuanaSubmitRequest;

//Services
use App\Http\Requests\Cebuana\AddMoneyCebuanaRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Services\AddmoneyCebuana\IAddMoneyCebuanaService;

class InAddMoneyCebuanaController extends Controller
{
    //
    private IAddMoneyCebuanaService $addMoneyCebuanaService;
    private IResponseService $responseService;

    public function __construct(IAddMoneyCebuanaService $addMoneyCebuanaService, IResponseService $responseService)
    {
        $this->addMoneyCebuanaService = $addMoneyCebuanaService;
        $this->responseService = $responseService;
    }

    // public function addMoney(AddMoneyCebuanaRequest $request)
    // {
        
    //     //alternative getting user account id
    //     $data = $request->all();
    //     $userId = $data['user_account_id'];

    //     // $data = $request->validated();
    //     // $userId = $request->user()->id; //cant get this auth user
    //     $response = $this->addMoneyCebuanaService->addMoney($userId, $data);
    //     return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    // }

    public function generate() {
        $response = $this->addMoneyCebuanaService->generate(request()->user()->id, request()->user()->tier_id);
        return $this->responseService->successResponse($response->toArray(), SuccessMessages::success);
    }

    public function submit(CebuanaSubmitRequest $request) {
        $response = $this->addMoneyCebuanaService->submit($request->all());
        return $this->responseService->successResponse($response, SuccessMessages::success);
    }
}
