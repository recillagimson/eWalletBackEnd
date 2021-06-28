<?php

namespace App\Http\Controllers;

use App\Enums\SuccessMessages;
use App\Http\Requests\DrcrMemo\ApprovalRequest;
use App\Http\Requests\DrcrMemo\DrcrMemoRequest;
use App\Http\Requests\DrcrMemo\GetUserRequest;
use App\Http\Requests\DrcrMemo\ShowRequest;
use App\Services\DrcrMemo\IDrcrMemoService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;

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
     * @param ShowRequest $request
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
     * @param GetUserRequest $request
     * @return JsonResponse
     */
    public function getUser(GetUserRequest $request): JsonResponse
    {
        $data = $request->route('accountNumber');
        $getUser = $this->drcrMemoService->getUser($data);
        return $this->responseService->successResponse($getUser, SuccessMessages::success);
    }


    /**
     * Show all pending status
     *
     * @param ShowRequest $request
     * @return JsonResponse
     */
    public function showPending(ShowRequest $request): JsonResponse
    {
        return $this->drcrMemoService->showPending($request->user());
        //return $this->responseService->successResponse($showPending, SuccessMessages::success);
    }


    /**
     * Store Drcr memo
     *
     * @param DrcrMemoRequest $request
     * @return JsonResponse
     */
    public function store(DrcrMemoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $store = $this->drcrMemoService->store($request->user(), $data);
        return $this->responseService->successResponse($store->toArray(), SuccessMessages::success);
    }

    /**
     * Approval of Drcr Memo
     *
     * @param ApprovalRequest $request
     * @return JsonResponse
     */
    public function approval(ApprovalRequest $request): JsonResponse
    {
        $data = $request->validated();
        $approval = $this->drcrMemoService->approval($request->user(), $data);
        return $this->responseService->successResponse($approval, SuccessMessages::success);
    }

}
