<?php

namespace App\Http\Controllers\Tier;

use App\Enums\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tier\TierApprovalRequest;
use App\Http\Requests\User\SendEmailRequest;
use App\Http\Requests\User\SendSMSRequest;
use App\Models\TierApproval;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Services\Tier\ITierApprovalService;
use App\Services\Utilities\Responses\IResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->all();
        $records = $this->iTierApprovalRepository->list($params);
        return $this->responseService->successResponse($records->toArray(), SuccessMessages::success);
    }


    /**
     * Display the specified resource.
     *
     * @param TierApproval $tierApproval
     * @return Response
     */
    public function show(TierApproval $tierApproval): JsonResponse
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
     * @param TierApprovalRequest $request
     * @param TierApproval $tierApproval
     * @return JsonResponse
     */
    public function update(TierApprovalRequest $request, TierApproval $tierApproval): JsonResponse
    {
        $params = $request->all();
        $params['actioned_by'] = request()->user()->id;
        $record = $this->iTierApprovalService->updateStatus($params, $tierApproval);
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::recordDeleted);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TierApproval $tierApproval
     * @return JsonResponse
     */
    public function destroy(TierApproval $tierApproval): JsonResponse
    {
        $this->iTierApprovalRepository->delete($tierApproval);
        return $this->responseService->noContentResponse("", SuccessMessages::success);
    }

    public function sendEmail(SendEmailRequest $request): JsonResponse
    {
        $email = $request->email;
        $this->iTierApprovalService->sendEmail($email, $request->message);
        return $this->responseService->successResponse([], SuccessMessages::success);
    }

    public function sendSMS(SendSMSRequest $request): JsonResponse
    {
        $user = $request->mobile_number;
        $this->iTierApprovalService->sendSMS($user, $request->message);
        return $this->responseService->successResponse([], SuccessMessages::success);
    }
}
