<?php

namespace App\Http\Controllers\WhiteList;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Repositories\WhiteList\IWhiteListRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\WhiteList\CreateWhiteListRequest;

class WhiteListController extends Controller
{
    private IWhiteListRepository $whiteListRepo;
    private IResponseService $responseService;

    public function __construct(
        IWhiteListRepository $whiteListRepo,
        IResponseService $responseService
    )
    {
        $this->whiteListRepo = $whiteListRepo;
        $this->responseService = $responseService;
    }

    public function index() {
        $records = $this->whiteListRepo->getAll();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::addMoneyCancel);
    }

    public function store(CreateWhiteListRequest $request) {
        $records = $this->whiteListRepo->create($request->all());
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::addMoneyCancel);
    }
}
