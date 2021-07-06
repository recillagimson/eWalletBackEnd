<?php

namespace App\Http\Controllers\Log;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Repositories\LogHistory\ILogHistoryRepository;
use App\Services\Utilities\Responses\IResponseService;

class LogHistoryController extends Controller
{
    private ILogHistoryRepository $logHistory;
    private IResponseService $responseService;
    public function __construct(ILogHistoryRepository $logHistory, IResponseService $responseService)
    {
        $this->logHistory = $logHistory;
        $this->responseService = $responseService;
    }

    public function index(Request $request) {
        $user_account_id = 0;
        if($request->has('user_account_id')) {
            $user_account_id = $request->user_account_id;
        }
        $records = $this->logHistory->getByUserAccountId($user_account_id);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }
}
