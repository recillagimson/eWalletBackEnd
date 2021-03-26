<?php

namespace App\Http\Controllers;

use App\Enums\PayloadTypes;
use App\Http\Requests\Payload\DecryptRequest;
use App\Http\Requests\Payload\EncryptRequest;
use App\Models\Payload;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PayloadController extends Controller
{
    public IEncryptionService $encryptionService;

    public function __construct(IEncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Generates a key to encrypt requests on front-end
     * application.
     *
     * @return JsonResponse
     */
    public function generate(): JsonResponse
    {
        $passPhrase = Str::random(16);

        $newPayload = $this->encryptionService->payloads->create([
            'payloadType' => PayloadTypes::Request,
            'passPhrase' => $passPhrase
        ]);

        return response()->json(['id' => $newPayload->id, 'passPhrase' => $newPayload->passPhrase], 200);
    }

    /**
     * Gets the corresponding key to decrypt responses
     * on front-end applications
     *
     * @param Payload $payload
     * @return JsonResponse
     */
    public function getResponseKey(Payload $payload): JsonResponse
    {
        $this->encryptionService->payloads->delete($payload);
        return response()->json(['id' => $payload->id, 'passPhrase' => $payload->passPhrase], 200);
    }

    /**
     * Utility to encrypt json. Only available for local
     * environment.
     *
     * @param EncryptRequest $request
     * @return JsonResponse
     */
    public function encrypt(EncryptRequest $request): JsonResponse
    {
        $data = $request->validated();
        $responseData = $this->encryptionService->encrypt($data['payload'], $data['passPhrase']);
        return response()->json($responseData, Response::HTTP_OK);
    }

    /**
     * Utility to decrypt json. Only available for local
     * environment.
     *
     * @param DecryptRequest $request
     * @return JsonResponse
     */
    public function decrypt(DecryptRequest $request): JsonResponse
    {
        $data = $request->validated();
        $responseData = $this->encryptionService->decrypt($data['payload'], $data['id']);
        return response()->json($responseData, Response::HTTP_OK);
    }
}
