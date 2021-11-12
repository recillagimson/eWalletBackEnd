<?php

return [
    'appId' => env('KYC_APP_ID'),
    'appKey' => env('KYC_APP_KEY'),
    'faceMatchUrl' => env('KYC_APP_FACEMATCH_URL'),
    'ocrUrl' => env('KYC_APP_OCR_URL'),
    'ocrPassportUrl' => env('KYC_APP_OCR_URL_PASSPORT'),
    'verifyUrl' => env('KYC_APP_VERIFY_URL'),
    'callbackUrl' => env('KYC_APP_CALLBACK_URL'),
    'enrolId' => env('KYC_APP_VERIFY_REGISTER_ID', 'no'),
    'verifyUrlV2' => env('KYC_APP_VERIFY_URL_V2'),
    'faceAuthUrl' => env('KYC_APP_FACE_AUTH_URL'),
    'delay' => env('KYC_APP_DELAY_TIME', 5)
];