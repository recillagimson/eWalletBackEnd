<?php

namespace App\Http\Controllers\Tier;

use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Models\TierApprovalComment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tier\TierApprovalCommentRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Repositories\Tier\ITierApprovalCommentRepository;

class TierApprovalCommentController extends Controller
{
    private ITierApprovalCommentRepository $iTierApprovalCommentService;
    private IResponseService $responseService;


    public function __construct(
        IResponseService $responseService,
        ITierApprovalCommentRepository $iTierApprovalCommentService
    )
    {
        $this->iTierApprovalCommentService = $iTierApprovalCommentService;
        $this->responseService = $responseService;
    }

    public function list(Request $request) {
        $params = $request->all();
        $records = $this->iTierApprovalCommentService->listTierApprovalComments($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }

    public function create(TierApprovalCommentRequest $request) {
        $params = $request->all();
        $params['user_created'] = request()->user()->id;
        $params['user_updated'] = request()->user()->id;
        $records = $this->iTierApprovalCommentService->create($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }
}
