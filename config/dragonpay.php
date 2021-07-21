<?php

return [
    /**
     * DragonPay Username UAT
     * This value will be added as username
     * to the http url params when sending
     * request to the DragonPay API
     */
    'dp_merchantID' => env('DP_MERCHANTID'),

    /**
     * DragonPay Password UAT
     * This value will be added as password
     * to the http url params when sending
     * request to the DragonPay API
     */
    'dp_key' => env('DP_KEY'),

    /**
     * DragonPay URL UAT
     * This value will be used as the base url
     * for DragonPay API. Add the parameters
     * after this base URL
     */
    'dp_base_url' => env('DP_BASE_URL'),
];
