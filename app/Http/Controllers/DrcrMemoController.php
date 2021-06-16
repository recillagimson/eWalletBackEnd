<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DrcrMemo\ApprovalRequest;
use App\Http\Requests\DrcrMemo\DrcrMemoRequest;
use App\Http\Requests\DrcrMemo\GetUserRequest;
use App\Http\Requests\PayBills\PayBillsRequest;
use App\Models\DrcrMemos;
use App\Models\UserAccount;
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

    public function index() : JsonResponse
    {
        $list = $this->drcrMemoService->getList(request()->user());
        return $this->responseService->successResponse($list->toArray(), SuccessMessages::success);
    }

    public function show(DrcrMemos $drcrMemo): JsonResponse
    {
        return $this->responseService->successResponse($drcrMemo->toArray(), SuccessMessages::success);
    }


    public function getUser(GetUserRequest $request)
    {
        $accountNo = $request->route('account_no');
        return $this->drcrMemoService->getUser($accountNo);
    }

    
    public function store(DrcrMemoRequest $request): JsonResponse
    {
        $data = $request->post();
        $store = $this->drcrMemoService->store($request->user(), $data);
        return $this->responseService->successResponse($store->toArray(), SuccessMessages::success);
    }

    /**
     * Approval of Drcr Memo 
     *
     * @param ApprovalRequest $request
     * @param array $data
     * @return JsonResponse
     */
    public function approval(ApprovalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $approval = $this->drcrMemoService->approval($request->user(), $data);
        return $this->responseService->successResponse($approval->toArray(), SuccessMessages::success);
    }

}
