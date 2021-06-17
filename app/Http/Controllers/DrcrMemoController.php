<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DrcrMemo\ApprovalRequest;
use App\Http\Requests\DrcrMemo\DrcrMemoRequest;
use App\Http\Requests\DrcrMemo\GetUserRequest;
use App\Http\Requests\DrcrMemo\ShowRequest;
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



    /**
     * Get all the list of DRCR Memo 
     *
     * @param DrcrMemoRequest $request
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $list = $this->drcrMemoService->getList(request()->user());
        return $this->responseService->successResponse($list->toArray(), SuccessMessages::success);
    }


    /**
     * Show data using DRCR Memo id
     *
     * @param DrcrMemoRequest $request
     * @return JsonResponse
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $referenceNumber = $request->route('referenceNumber');
        $show = $this->drcrMemoService->show($referenceNumber);
        return $this->responseService->successResponse($show, SuccessMessages::success);
    }


    /**
     * Get's user by using account number 
     *
     * @param DrcrMemoRequest $request
     * @param object &getUser
     * @param array $accountNo
     * @return JsonResponse
     */
    public function getUser(GetUserRequest $request): JsonResponse
    {
        $data = $request->route('accountNumber');
        $getUser = $this->drcrMemoService->getUser($data);
        return $this->responseService->successResponse($getUser, SuccessMessages::success);
    }


    /**
     * Store Drcr memo 
     *
     * @param DrcrMemoRequest $request
     * @param object &store
     * @param array $data
     * @return JsonResponse
     */
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
        return $this->responseService->successResponse($approval, SuccessMessages::success);
    }

}
