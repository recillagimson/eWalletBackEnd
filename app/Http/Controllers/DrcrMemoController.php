<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DrcrMemo\DrcrMemoRequest;
use App\Http\Requests\PayBills\PayBillsRequest;
use App\Services\DrcrMemo\DrcrMemoService;
use App\Services\DrcrMemo\IDrcrMemoService;
use App\Services\PayBills\IPayBillsService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Request;
use Illuminate\Http\Response;

class DrcrMemoController extends Controller
{
    private IDrcrMemoService $drcrMemoService;
    private IResponseService $responseService;

    public function __construct(IDrcrMemoService $drcrMemoService, IResponseService $responseService)
    {
        $this->drcrMemoService = $drcrMemoService;
        $this->responseService = $responseService;
    }

    public function index()
    {
        $list = $this->drcrMemoService->getList();
        return $this->responseService->successResponse($list->toArray(), SuccessMessages::success);
    }

    public function store(DrcrMemoRequest $request)//: JsonResponse
    {
        $data = $request->post();
        $store = $this->drcrMemoService->store($data, $request->user());
        return $this->responseService->successResponse($store->toArray(), SuccessMessages::success);
    }

}
