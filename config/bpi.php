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

    'bpi_codes' => [
        'FTUBE002' => 'Your One-Time PIN has been suspended after 3 invalid attempts. Please try again after 10 minutes',
        'FTUBE003' => 'You have entered an invalid One-Time PIN. Please try again',
        'FTUBE004' => 'Your One-Time PIN expired. Please try again',
        'FTUBE005' => 'Transaction request has already been processed',
        'FTUBE007' => 'Transaction already processed but encountered an error',
        'FTUBE009' => 'Source account is not eligible for this transaction',
        'FTUBE0010' => 'You have insufficient balance in your account',
        'FTUBE0011' => 'You have exceeded the maximum transaction limit set by BPI for this type of transaction',
    ]
];
