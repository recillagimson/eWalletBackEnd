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
    'dp_url' => env('DP_URL'),

    /**
     * DragonPay Base URL UAT (V1)
     * This value will be used as base URL
     * to get the actual webservice URL 
     * for DragonPay
     */
    'dp_base_url_v1' => env('DP_BASE_URL'),
];