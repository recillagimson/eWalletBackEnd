<?php


namespace App\Services\Utilities\Responses;


use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseService implements IResponseService
{
    private IEncryptionService $encryptionService;

    public function __construct(IEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function successResponse(array $data = null, string $message = 'Success'): JsonResponse
    {
        return $this->success($message, Response::HTTP_OK, $data);
    }

    public function createdResponse(array $data = null, string $message = 'Success'): JsonResponse
    {
        return $this->success($message, Response::HTTP_CREATED, $data);
    }

    public function notFound(string $message): JsonResponse
    {
        $response = [
            'message' => $message
        ];

        return response()->json($response, Response::HTTP_NOT_FOUND);
    }

    private function success(string $message, int $statusCode, array $data = null): JsonResponse
    {
        $response = [
            'message' => $message,
        ];

        if ($data) $response['data'] = $this->encryptionService->encrypt($data);

        return response()->json($response, $statusCode);
    }

}