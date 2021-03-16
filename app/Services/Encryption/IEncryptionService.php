<?php
namespace App\Services\Encryption;

interface IEncryptionService {
    public function encrypt($data);
    public function decrypt($data, $reqId);
}
