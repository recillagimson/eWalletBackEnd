<?php

namespace App\Http\Controllers\Tier;

use App\Models\TierApproval;
use Illuminate\Http\Request;
use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Repositories\Tier\ITierRepository;
use App\Services\Tier\ITierApprovalService;
use App\Http\Requests\Tier\TierUpgradeRequest;
use App\Http\Requests\Tier\TierApprovalRequest;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\User\SendEmailRequest;
use App\Http\Requests\User\SendSMSRequest;

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
        return $this->responseService
            ->successResponse(
                $this->iTierApprovalRepository
                ->showTierApproval($tierApproval)
                ->toArray(), SuccessMessages::success);
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
        $params = $request->all();
        $params['actioned_by'] = request()->user()->id;
        $record = $this->iTierApprovalService->updateStatus($params, $tierApproval);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::recordDeleted);
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
        return $this->responseService->noContentResponse("", SuccessMessages::success);
    }
<<<<<<< HEAD
=======

    public function sendEmail(SendEmailRequest $request)
    {
        $email = $request->email;
        $this->iTierApprovalService->sendEmail($email, $request->message);
        return $this->responseService->successResponse([], SuccessMessages::success);
    }

    public function sendSMS(SendSMSRequest $request)
    {
        $user = $request->mobile_number;
        $this->iTierApprovalService->sendSMS($user, $request->message);
        return $this->responseService->successResponse([], SuccessMessages::success);
    }
>>>>>>> stagingfix
}
