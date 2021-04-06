<?php

return [
    /**
     * DragonPay Username UAT
     * This value will be added as username 
     * to the http url params when sending 
     * request to the DragonPay API
     */
    'dp_uat_merchantID' => env('DP_UAT_MERCHANTID'),

    /**
     * DragonPay Password UAT
     * This value will be added as password 
     * to the http url params when sending 
     * request to the DragonPay API
     */
    'dp_uat_key' => env('DP_UAT_KEY'),

    /**
     * DragonPay URL UAT
     * This value will be used as the base url 
     * for DragonPay API. Add the parameters 
     * after this base URL
     */
    'dp_uat_url' => env('DP_UAT_URL'),

    /**
     * DragonPay Base URL UAT (V1)
     * This value will be used as base URL
     * to get the actual webservice URL 
     * for DragonPay
     */
    'dp_uat_base_url_v1' => env('DP_UAT_BASE_URL_V1'),
];