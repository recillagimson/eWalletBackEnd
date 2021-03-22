<?php
namespace App\Services\Encryption;

use App\Enums\PayloadTypes;
use App\Repositories\Payload\IPayloadRepository;
use Illuminate\Support\Str;

class EncryptionService implements IEncryptionService {

    public IPayloadRepository $payloads;

    public function __construct(IPayloadRepository $payloads)
    {
        $this->payloads = $payloads;
    }

    public function encrypt($data)
    {
        $passPhrase = Str::random(16);
        $newPayload = $this->payloads->create([
            'payloadType' => PayloadTypes::Request,
            'passPhrase' => $passPhrase
        ]);


        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';

        while (strlen($salted) < 48) {
            $dx = md5($dx.$passPhrase.$salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv  = substr($salted, 32,16);
        $encrypted_data = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));

        return [
            'id' => $newPayload->id,
            'payload' => json_encode($data)
        ];
    }

    public function decrypt($data, $reqId)
    {
        $payload = $this->payloads->get($reqId);
        if(!$payload) return null;

        $jsondata = json_decode($data, true);
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv  = hex2bin($jsondata["iv"]);
        $concatedPassphrase = $payload->passPhrase.$salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);

        $this->payloads->delete($payload);
        return json_decode($data, true);
    }
}
