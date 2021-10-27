<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use App\Services\Tier\ITierApprovalService;
use App\Services\Encryption\IEncryptionService;
use App\Repositories\Tier\ITierApprovalRepository;
use App\Http\Requests\UserPhoto\PhotoActionRequest;
use App\Http\Requests\UserPhoto\SelfieActionRequest;
use App\Http\Requests\UserPhoto\SelfieUploadRequest;
use App\Http\Requests\UserPhoto\VerificationRequest;
use App\Http\Requests\UserPhoto\ManualIDUploadRequest;
use App\Services\Utilities\Responses\IResponseService;
use App\Http\Requests\UserPhoto\ManualSelfieUploadRequest;
use App\Http\Requests\UserSignature\UploadSignatureRequest;
use App\Services\Utilities\Verification\IVerificationService;

class UserPhotoController extends Controller
{
    private IVerificationService $iVerificationService;
    private IResponseService $responseService;
    private ITierApprovalRepository $tierApproval;
    private ITierApprovalService $tierApprovalService;


    public function __construct(IResponseService $responseService,
                                IVerificationService $iVerificationService,
                                ITierApprovalRepository $tierApproval,
                                ITierApprovalService $tierApprovalService)
    {
        $this->responseService = $responseService;
        $this->iVerificationService = $iVerificationService;
        $this->tierApproval = $tierApproval;
        $this->tierApprovalService = $tierApprovalService;
    }


    public function createVerification(VerificationRequest $request) {
        $params = $request->all();
        $params['user_account_id'] = request()->user()->id;
        $createRecord = $this->iVerificationService->create($params);
        // $encryptedResponse = $this->encryptionService->encrypt(array($createRecord));
        // return response()->json($encryptedResponse, Response::HTTP_OK);
        return $this->responseService->successResponse($createRecord, SuccessMessages::success);
    }

    public function createSelfieVerification(SelfieUploadRequest $request) {
        $params = $request->all();
        $createRecord = $this->iVerificationService->createSelfieVerification($params);
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }

    public function uploadSignature(UploadSignatureRequest $request) {
        $params = $request->all();
        $createRecord = $this->iVerificationService->uploadSignature($params);
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }

    public function getImageSignedUrl(string $userPhotoId) {
        $url = $this->iVerificationService->getSignedUrl($userPhotoId);
        return $this->responseService->successResponse(['image_url' => $url], SuccessMessages::success);
    }

    public function uploadIdManually(ManualIDUploadRequest $request) {
        $attr = $request->all();
        $user_account_id = $this->tierApproval->get($attr['tier_approval_id'])->user_account_id;
        $attr['user_account_id'] = $user_account_id;
        $attr['id_number'] = $request->has('id_number') ? $attr['id_number'] : "";
        $createRecord = $this->iVerificationService->create($attr);
        return $this->responseService->successResponse($createRecord, SuccessMessages::success);
    }

    public function uploadSelfieManually(ManualSelfieUploadRequest $request) {
        $attr = $request->all();
        $user_account_id = $this->tierApproval->get($attr['tier_approval_id'])->user_account_id;
        $attr['user_account_id'] = $user_account_id;
        $createRecord = $this->iVerificationService->createSelfieVerification($attr, $attr['user_account_id']);
        return $this->responseService->successResponse($createRecord->toArray(), SuccessMessages::success);
    }

    public function takePhotoAction(PhotoActionRequest $request) {
        $record = $this->tierApprovalService->takePhotoAction($request->all());
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }

    public function takeSelfieAction(SelfieActionRequest $request) {
        $record = $this->tierApprovalService->takeSelfieAction($request->all());
        return $this->responseService->successResponse($record->toArray(), SuccessMessages::success);
    }
}
