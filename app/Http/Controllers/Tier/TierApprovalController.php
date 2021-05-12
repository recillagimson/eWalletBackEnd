<?php

namespace App\Http\Controllers\Tier;

use App\Models\TierApproval;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Repositories\Tier\ITierRepository;
use App\Services\Tier\ITierApprovalService;
use App\Http\Requests\Tier\TierApprovalRequest;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Services\Utilities\Responses\IResponseService;

class TierApprovalController extends Controller
{

    private ITierApprovalRepository $iTierApprovalRepository;
    private ITierApprovalService $iTierApprovalService;
    private IResponseService $responseService;


    public function __construct(
        ITierApprovalRepository $iTierApprovalRepository,
        IResponseService $responseService,
        ITierApprovalService $iTierApprovalService
    )
    {
        // $this->encryptionService = $encryptionService;
        $this->iTierApprovalRepository = $iTierApprovalRepository;
        $this->iTierApprovalService = $iTierApprovalService;
        $this->responseService = $responseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $records = $this->iTierApprovalRepository->list($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TierApproval $tierApproval)
    {
        $record = $this->responseService->successResponse($tierApproval->toArray(), SuccessMessages::success);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TierApprovalRequest $request, TierApproval $tierApproval)
    {
        $record = $this->iTierApprovalService->updateStatus($request->all(), $tierApproval);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TierApproval $tierApproval)
    {
        $this->iTierApprovalRepository->delete($tierApproval);
        return $this->responseService->noContentResponse("", SuccessMessages::recordDeleted);
    }
}
