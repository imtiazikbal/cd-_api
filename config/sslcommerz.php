<?php

return [
    'store_id'       => env('STORE_ID', 'funne644e48db5ede2'),
    'store_password' => env('STORE_PASSWORD', 'funne644e48db5ede2@ssl'),
    'is_production'  => env('IS_PRODUCTION', false),
    'api_domain'     => [
        'sandbox'    => 'https://sandbox.sslcommerz.com',
        'production' => 'https://securepay.sslcommerz.com',
    ],
    'api_url' => [
        'init_payment'       => '/gwprocess/v4/api.php',
        'transaction_status' => '/validator/api/merchantTransIDvalidationAPI.php',
        'order_validate'     => '/validator/api/validationserverAPI.php'
    ],
    'callback_domain' => 'http://localhost:3000/'
];
