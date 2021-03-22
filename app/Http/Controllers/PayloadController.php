<?php

namespace App\Http\Controllers;

use App\Enums\PayloadTypes;
use App\Models\Payload;
use App\Repositories\Payload\IPayloadRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PayloadController extends Controller
{
    public IPayloadRepository $payloads;

    public function __construct(IPayloadRepository $payloads)
    {
        $this->payloads = $payloads;
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

        $newPayload = $this->payloads->create([
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
        $this->payloads->delete($payload);
        return response()->json(['id' => $payload->id, 'passPhrase' => $payload->passPhrase], 200);
    }
}
