<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Sms Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default sms "driver" that will be used on
    | sending sms . By default, we will use the elitbuzz sms driver but
    | you may specify any of the other wonderful drivers provided here.
    |
    |
    */

    'revesms' => env('REVESMS'),
    'elitbuzzsms' => env('ELITBUZZSMS'),
    'elitbuzz' => [
        'server' => 'https://880sms.com/smsapi'
    ],
    'revesms' => [
        'server' => 'http://apismpp.revesms.com/sendtext'
    ],
];