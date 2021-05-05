<?php

return [
    'base_url' => env('UBP_BASE_URL'),
    'token_url' => env('UBP_TOKEN_URL'),

    'pesonet_transfer_url' => env('UBP_PESONET_TRANSFER_URL'),
    'pesonet_transaction_update_url' => env('UBP_PESONET_TRANSACTION_UPDATE_URL'),
    'pesonet_banks_url' => env('UBP_PESONET_BANKS_URL'),

    'instapay_transfer_url' => env('UBP_INSTAPAY_TRANSFER_URL'),
    'instapay_banks_url' => env('UBP_INSTAPAY_BANKS_URL'),

    'direct_ubp_transfer_url' => env('UBP_PARTNER_TRANSFER_URL'),

    'client_id' => env('UBP_CLIENT_ID'),
    'client_secret' => env('UBP_CLIENT_SECRET'),
    'partner_id' => env('UBP_PARTNER_ID'),
    'username' => env('UBP_USERNAME'),
    'password' => env('UBP_PASSWORD'),
    'scopes' => env('UBP_SCOPES')
];
