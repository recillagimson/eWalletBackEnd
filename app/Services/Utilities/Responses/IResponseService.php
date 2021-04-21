<?php

namespace App\Services\Utilities\Responses;

use Illuminate\Http\JsonResponse;

interface IResponseService
{
    public function successResponse(array $data = null, string $message = 'Success'): JsonResponse;

    public function createdResponse(array $data = null, string $message = 'Success'): JsonResponse;

    public function notFound(string $message): JsonResponse;
}
