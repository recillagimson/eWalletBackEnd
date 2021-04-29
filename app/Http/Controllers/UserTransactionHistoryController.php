<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\UserTransactionHistory\IUserTransactionHistoryRepository;

class UserTransactionHistoryController extends Controller
{
    private IUserTransactionHistoryRepository $userTransactionHistory;
    private IResponseService $responseService;


    public function __construct(IResponseService $responseService,
        IUserTransactionHistoryRepository $userTransactionHistory)
    {
        $this->responseService = $responseService;
        $this->userTransactionHistory = $userTransactionHistory;
    }

    public function index() {
        $records = $this->userTransactionHistory->getByAuthUser();
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function show(string $id) {
        $record = $this->userTransactionHistory->findTransactionWithRelation($id);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }
}
