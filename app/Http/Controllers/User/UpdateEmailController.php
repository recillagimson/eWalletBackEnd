<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateEmailRequest;
use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Services\Utilities\Responses\IResponseService;
use App\Traits\UserHelpers;
use App\Services\UserAccount\IUserAccountService;

class UpdateEmailController extends Controller
{
    use UserHelpers; 

    private IUserAccountService $updateEmailService;
    private IResponseService $responseService;

    public function __construct(IUserAccountService $updateEmailService, IResponseService $responseService)
    {
        $this->updateEmailService = $updateEmailService;
        $this->responseService = $responseService;
    }

    public function validateEmail(UpdateEmailRequest $request)
    {
        $fillRequest = $request->validated();
        $emailField = $this->getEmailField($request);
        $review = $this->updateEmailService->validateEmail($emailField, $fillRequest[$emailField]);

        return $this->responseService->successResponse([
            $emailField => $fillRequest[$emailField]
        ], SuccessMessages::validateUpdateEmail);
    }

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $fillRequest = $request->validated();
        $emailField = $this->getEmailField($request);
        $postback = $this->updateEmailService->updateEmail($emailField, $fillRequest[$emailField], $request->user());

        return $this->responseService->successResponse($postback, SuccessMessages::updateEmailSuccessful);
    }
}
