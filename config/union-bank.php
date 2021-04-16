<?php

return [
    /**
     * ----------------------------------------------------------
     * Get instapay bank list API
     * ----------------------------------------------------------
     * 
     * This will be use for the header of the API
     * 
     */
    'ubp_api_url' => env('UBP_INSTAPAY_UAT_URL', 'https://api.unionbankph.com/ubp/external/partners/v3/instapay/banks'),
    'ubp_client_id' => env('UBP_UAT_CLIENT_ID', 'b1abe17f-c47f-4dd5-8a02-6cea05f9a219'),
    'ubp_client_secret' => env('UBP_UAT_CLIENT_SECRET', 'oMP3dqbL5nG8hM5pO4sT6nP5yH3kO6dG6nS8kP2uM5iX7xC0qJ2gE6bTtm'),
    'ubp_client_partner_id' => env('UBP_UAT_PARTNER_ID', 'e8d53436-be00-4547-9dcc-093aa5e594a8'),

    /**
     * -----------------------------------------------------------
     * Parameters for token API
     * -----------------------------------------------------------
     * 
     * This will be use as parameters in form-urlencoded
     * 
     */
    'ubp_token_url' =>  env('UBP_UAT_TOKEN_URL', 'https://api-uat.unionbankph.com/ubp/uat/partners/v1/oauth2/token'),
    'ubp_grant_type'    =>  env('UBP_UAT_GRANT_TYPE', 'password'),
    'ubp_username'  =>  env('UBP_UAT_USERNAME','squidpay'),
    'ubp_password'  =>  env('UBP_UAT_PASSWORD','$qUidPaY@2020'),
    'ubp_scope' =>  env('UBP_UAT_SCOPE','transfers transfers_pesonet account_inquiry instapay'),

    /**
     * -----------------------------------------------------------
     * Parameter for fund transfer single transaction API
     * -----------------------------------------------------------
     * 
     * This will be use as parameters in raw JSON format
     * 
     */
    'ubp_transfer_url'  =>  env('UBP_UAT_TRANSFER_URL','https://api-uat.unionbankph.com/ubp/uat/partners/v3/instapay/transfers/single'),
    'ubp_transfer_client'   =>  env('UBP_UAT_TRANSFER_CLIENT','0044210f-af84-42fb-8b6a-c5536a577dc6'),
    'ubp_transfer_secret'   =>  env('UBP_UAT_TRANSFER_SECRET','H6nG1bM2aX6wD3lL2bX8dA6nO8bP7hX2dS0bC0xE8oO0gP3pG7'),
    'ubp_transfer_partner'  =>  env('UBP_UAT_TRANSFER_PARTNER','6823e8df-7305-4acb-b62e-53a0ce8a2042')
];
