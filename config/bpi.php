<?php

return [
    'clientId' => env('BPI_CLIENT_ID'),
    'clientSecret' => env('BPI_CLIENT_SECRET'),
    'authUrl' => env('BPI_AUTH'),
    'transactionalUrl' => env('BPI_TRANSACTIONAL_ENDPOINT'),
    'fundTopUpUrl' => env('BPI_FUND_TOP_UP_ENDPOINT'),
    'fundTopUpOtpUrl' => env('BPI_FUND_TOP_UP_OTP'),
    'fundTopUpStatusUrl' => env('BPI_FUND_TOP_UP_STATUS'),
    'processUrl' => env('BPI_PROCESS_URL'),
];
