<?php

return [
    'base_url' => env('BC_BASE_URL'),
    'token_url' => env('BC_TOKEN_URL'),

    'biller_url' => env('BC_BILLERS_URL'),
    'biller_information_url' => env('BC_BILLER_INFORMATION_URL'),
    'otherChargesUrl' => env('BC_OTHER_CHARGES_URL'),

    'validate_account_url' => env('BC_VALIDATE_ACCOUNT_URL'),
    'create_payment_url' => env('BC_CREATE_PAYMENT_URL'),
    'inquire_payment_url' => env('BC_INQUIRE_PAYMENT_URL'),
    'get_wallet_balance_url' => env('BC_GET_WALLET_BALANCE_URL'),

    'tpa_id' => env('BC_TPA_ID'),
    'client_id' => env('BC_CLIENT_ID'),
    'client_secret' => env('BC_CLIENT_SECRET'),
    'scopes' => env('BC_SCOPES'),
];
