<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'load' => [
        'globe' => [
            'url' => env('GLOBE_URL'),
            'id' => env('GLOBE_APP_ID'),
            'secret' => env('GLOBE_APP_SECRET'),
            'rewards_token' => env('GLOBE_REWARDS_TOKEN'),
        ],
        'atm' => [
            'url' => env('ATM_PREPAID_SOLUTIONS_URL'),
            'products_url' => env('ATM_PREPAID_PRODUCTS_URL'),
            'prefix_url' => env('ATM_PREPAID_PREFIX_URL'),
            'telco_status_url' => env('ATM_PREPAID_TELCO_STATUS_URL'),
            'balance_url' => env('ATM_PREPAID_BALANCE_URL'),
            'topup_url' => env('ATM_PREPAID_TOPUP_URL'),
            'topup_inquiry_url' => env('ATM_PREPAID_TOPUP_INQUIRY_URL'),
            'topup_epin_url' => env('ATM_PREPAID_EPIN_URL'),
            'topup_epin_inquiry_url' => env('ATM_PREPAID_EPIN_INQUIRY_URL'),
            'id' => env('ATM_PREPAID_SOLUTIONS_ID'),
            'uid' => env('ATM_PREPAID_SOLUTIONS_UID'),
            'password' => env('ATM_PREPAID_SOLUTIONS_PASSWORD'),
            'key_password' => env('ATM_KEY_PASSWORD', ''),
        ],
    ]
];
