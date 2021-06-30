<?php

namespace App\Services\BPIService\PackageExtension;

use Tmilos\JoseJwt\Context\Context;
use Tmilos\JoseJwt\Util\StringUtils;
use Tmilos\JoseJwt\Error\JoseJwtException;
use Tmilos\JoseJwt\Util\UrlSafeB64Encoder;

class Encryption {
    /**
     * @param Context         $context
     * @param array|object    $payload
     * @param string|resource $key
     * @param string          $jwsAlgorithm
     * @param array           $extraHeaders
     *
     * @return string
     */
    
    public static function encode2(Context $context, $payload, $key, $jwsAlgorithm, $extraHeaders = [])
    {
        $header = array_merge([
            'alg' => '',
            // 'typ' => 'JWT',
        ], $extraHeaders);

        $hashAlgorithm = $context->jwsAlgorithms()->get($jwsAlgorithm);
        if (null == $hashAlgorithm) {
            throw new JoseJwtException(sprintf('Unknown algorithm "%s"', $jwsAlgorithm));
        }

        $header['alg'] = $jwsAlgorithm;

        $payloadString = StringUtils::payload2string($payload, $context->jsonMapper());

        $signingInput = implode('.', [
            UrlSafeB64Encoder::encode(json_encode($header)),
            UrlSafeB64Encoder::encode($payloadString),
        ]);

        $signature = $hashAlgorithm->sign($signingInput, $key);
        $signature = UrlSafeB64Encoder::encode($signature);

        return $signingInput.'.'.$signature;
    }

    public static function encodeJWE(Context $context, $payload, $key, $jweAlgorithm, $jweEncryption, array $extraHeaders = [])
    {
        if (empty($payload) || (is_string($payload) && trim($payload) == '')) {
            throw new JoseJwtException('Payload can not be empty');
        }
        $algorithm = $context->jweAlgorithms()->get($jweAlgorithm);
        if (null === $algorithm) {
            throw new JoseJwtException(sprintf('Invalid or unsupported algorithm "%s"', $jweAlgorithm));
        }
        $encryption = $context->jweEncryptions()->get($jweEncryption);
        if (null === $encryption) {
            throw new JoseJwtException(sprintf('Invalid or unsupported encryption "%s"', $jweEncryption));
        }

        $header = array_merge([
            'alg' => $jweAlgorithm,
            'enc' => $jweEncryption,
            // 'typ' => 'JWT',
        ], $extraHeaders);

        list($cek, $encryptedCek) = $algorithm->wrapNewKey($encryption->getKeySize(), $key, $header);

        $payloadString = StringUtils::payload2string($payload, $context->jsonMapper());

        $headerString = json_encode($header);
        $aad = UrlSafeB64Encoder::encode($headerString);
        $parts = $encryption->encrypt($aad, $payloadString, $cek);

        return implode('.', [
            UrlSafeB64Encoder::encode($headerString),
            UrlSafeB64Encoder::encode($encryptedCek),
            UrlSafeB64Encoder::encode($parts[0]),
            UrlSafeB64Encoder::encode($parts[1]),
            UrlSafeB64Encoder::encode($parts[2]),
        ]);
    }
}