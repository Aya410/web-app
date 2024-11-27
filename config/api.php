<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Here you may configure the rate limits for API requests. These rate
    | limits apply to different sections of your API. The values here are
    | configured as `limit` (maximum requests) and `period` (time in minutes).
    |
    */

    'rate_limits' => [
        'api' => [
            'limit' => 100,  // الحد الأقصى للطلبات
            'period' => 1,   // الوقت بالدقائق
        ],
        'sign_in' => [
            'limit' => 10,
            'period' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Other API Configurations
    |--------------------------------------------------------------------------
    |
    | You can add additional custom settings for your API here if needed.
    |
    */

];
