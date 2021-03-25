<?php
namespace App\Services\Encryption;

use App\Repositories\Payload\IPayloadRepository;

/**
 * @property IPayloadRepository $payloads
 *
 */
interface IEncryptionService {
    public function encrypt($data, string $passPhrase = null);
    public function decrypt(string $data, string $reqId, bool $deletePayload = true);
}
