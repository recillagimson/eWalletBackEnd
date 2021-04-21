<?php

namespace App\Services\Utilities\Responses;

use App\Enums\SuccessMessages;
use Illuminate\Http\JsonResponse;

interface IResponseService
{
    public function successResponse(array $data = null, string $message = SuccessMessages::success): JsonResponse;

    public function createdResponse(array $data = null, string $message = SuccessMessages::success): JsonResponse;

    public function noContentResponse(string $message = SuccessMessages::recordDeleted): JsonResponse;

    public function notFound(string $message): JsonResponse;
}
