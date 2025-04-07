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
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'courier' => [
        'key'    => env('STEADFAST_API_KEY'),
        'secret' => env('STEADFAST_SECRET_KEY'),
    ],

    'pathao' => [
        'sandbox'    => 'https://hermes-api.p-stageenv.xyz/',
        'production' => 'https://api-hermes.pathao.com/',
    ],

    'payment' => [
        'local'      => 'http://localhost:8001/api/payment/processing',
        'production' => 'https://softitcare.com/order/public/api/payment/processing',
    ],

    'bkash' => [
        'sandbox_url'           => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/',
        'production_url'        => 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/checkout/',
        'sandbox_user'          => 'sandboxTokenizedUser02',
        'sandbox_pass'          => 'sandboxTokenizedUser02@12345',
        'sandbox_app_key'       => '4f6o0cjiki2rfm34kfdadl1eqq',
        'sandbox_app_secret'    => '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',
        'production_user'       => '01894844446',
        'production_pass'       => ')@wN8aR(;wA',
        'production_app_key'    => '2QooJVs8QCaDHJSNVAT7dsZ2tc',
        'production_app_secret' => 'Jfr7P1mAraUBFCexUGzyW2suytl0GxQF0Skpp5t16cz6yJ7V7KUi',
    ],

    'nagad' => [
        'sandbox_url'                => 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/',
        'production_url'             => 'https://api.mynagad.com/',
        'sandbox_merchantId'         => '683002007104225',
        'sandbox_merchant_number'    => '01711428036',
        'sandbox_public_key'         => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjBH1pFNSSRKPuMcNxmU5jZ1x8K9LPFM4XSu11m7uCfLUSE4SEjL30w3ockFvwAcuJffCUwtSpbjr34cSTD7EFG1Jqk9Gg0fQCKvPaU54jjMJoP2toR9fGmQV7y9fz31UVxSk97AqWZZLJBT2lmv76AgpVV0k0xtb/0VIv8pd/j6TIz9SFfsTQOugHkhyRzzhvZisiKzOAAWNX8RMpG+iqQi4p9W9VrmmiCfFDmLFnMrwhncnMsvlXB8QSJCq2irrx3HG0SJJCbS5+atz+E1iqO8QaPJ05snxv82Mf4NlZ4gZK0Pq/VvJ20lSkR+0nk+s/v3BgIyle78wjZP1vWLU4wIDAQAB',
        'sandbox_private_key'        => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCJakyLqojWTDAVUdNJLvuXhROV+LXymqnukBrmiWwTYnJYm9r5cKHj1hYQRhU5eiy6NmFVJqJtwpxyyDSCWSoSmIQMoO2KjYyB5cDajRF45v1GmSeyiIn0hl55qM8ohJGjXQVPfXiqEB5c5REJ8Toy83gzGE3ApmLipoegnwMkewsTNDbe5xZdxN1qfKiRiCL720FtQfIwPDp9ZqbG2OQbdyZUB8I08irKJ0x/psM4SjXasglHBK5G1DX7BmwcB/PRbC0cHYy3pXDmLI8pZl1NehLzbav0Y4fP4MdnpQnfzZJdpaGVE0oI15lq+KZ0tbllNcS+/4MSwW+afvOw9bazAgMBAAECggEAIkenUsw3GKam9BqWh9I1p0Xmbeo+kYftznqai1pK4McVWW9//+wOJsU4edTR5KXK1KVOQKzDpnf/CU9SchYGPd9YScI3n/HR1HHZW2wHqM6O7na0hYA0UhDXLqhjDWuM3WEOOxdE67/bozbtujo4V4+PM8fjVaTsVDhQ60vfv9CnJJ7dLnhqcoovidOwZTHwG+pQtAwbX0ICgKSrc0elv8ZtfwlEvgIrtSiLAO1/CAf+uReUXyBCZhS4Xl7LroKZGiZ80/JE5mc67V/yImVKHBe0aZwgDHgtHh63/50/cAyuUfKyreAH0VLEwy54UCGramPQqYlIReMEbi6U4GC5AQKBgQDfDnHCH1rBvBWfkxPivl/yNKmENBkVikGWBwHNA3wVQ+xZ1Oqmjw3zuHY0xOH0GtK8l3Jy5dRL4DYlwB1qgd/Cxh0mmOv7/C3SviRk7W6FKqdpJLyaE/bqI9AmRCZBpX2PMje6Mm8QHp6+1QpPnN/SenOvoQg/WWYM1DNXUJsfMwKBgQCdtddE7A5IBvgZX2o9vTLZY/3KVuHgJm9dQNbfvtXw+IQfwssPqjrvoU6hPBWHbCZl6FCl2tRh/QfYR/N7H2PvRFfbbeWHw9+xwFP1pdgMug4cTAt4rkRJRLjEnZCNvSMVHrri+fAgpv296nOhwmY/qw5Smi9rMkRY6BoNCiEKgQKBgAaRnFQFLF0MNu7OHAXPaW/ukRdtmVeDDM9oQWtSMPNHXsx+crKY/+YvhnujWKwhphcbtqkfj5L0dWPDNpqOXJKV1wHt+vUexhKwus2mGF0flnKIPG2lLN5UU6rs0tuYDgyLhAyds5ub6zzfdUBG9Gh0ZrfDXETRUyoJjcGChC71AoGAfmSciL0SWQFU1qjUcXRvCzCK1h25WrYS7E6pppm/xia1ZOrtaLmKEEBbzvZjXqv7PhLoh3OQYJO0NM69QMCQi9JfAxnZKWx+m2tDHozyUIjQBDehve8UBRBRcCnDDwU015lQN9YNb23Fz+3VDB/LaF1D1kmBlUys3//r2OV0Q4ECgYBnpo6ZFmrHvV9IMIGjP7XIlVa1uiMCt41FVyINB9SJnamGGauW/pyENvEVh+ueuthSg37e/l0Xu0nm/XGqyKCqkAfBbL2Uj/j5FyDFrpF27PkANDo99CdqL5A4NQzZ69QRlCQ4wnNCq6GsYy2WEJyU2D+K8EBSQcwLsrI7QL7fvQ==',
        'production_merchantId'      => '688100452557375',
        'production_merchant_number' => '01810045255',
        'production_public_key'      => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiCWvxDZZesS1g1lQfilVt8l3X5aMbXg5WOCYdG7q5C+Qevw0upm3tyYiKIwzXbqexnPNTHwRU7Ul7t8jP6nNVS/jLm35WFy6G9qRyXqMc1dHlwjpYwRNovLc12iTn1C5lCqIfiT+B/O/py1eIwNXgqQf39GDMJ3SesonowWioMJNXm3o80wscLMwjeezYGsyHcrnyYI2LnwfIMTSVN4T92Yy77SmE8xPydcdkgUaFxhK16qCGXMV3mF/VFx67LpZm8Sw3v135hxYX8wG1tCBKlL4psJF4+9vSy4W+8R5ieeqhrvRH+2MKLiKbDnewzKonFLbn2aKNrJefXYY7klaawIDAQAB',
        'production_private_key'     => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDeswKtYGV/4TaG9FJA2iiwnvUXz9rW2irs6XsEi+rQNf6IV2MqJtegV818BM7GtoI+1sT90rVJDU5K1w7eKH53X72q3+mB8RP+xwMN++EP63/DurIXKDc2i3L+NxeFDnGf8VOsDaHvT9cbn0VGM4ZHiuvI7t0TR4vJIdBFer7gj1s7c+vd2n+RnfLIN05kq3L8dg0Ygydmny5/TXfea2iWUktJhe2kqwseC3uvfZj+Bws+DETzMrQxvnW8N2A0Ls5HOKKlgEoA/akbayV1VV0wFvKA3OIkKePxgMOxPLvOhL6NggSnCQWIgZKa3U1daI6NfVAHdg3ctc7zqrTeK2UbAgMBAAECggEAP8x6zN6b1VnYvCrMUaXuGENBpdO3NuwDdiGhS3kmCQYe6EJYt1+vUFE3HftmnYj1oTj38Ftn8gis0EXyDXT+XgaAmK5TABXni4rJ9jydimkGDjWzBW1Q/ABRLkDsLQMpTA/fz5+ML7novxIOz4C9TEuhJsw/a2GIy1MzjmQOwNfn2SQB+OJydtGq2FF7h9DBPjjRUOtSgknn48JDz08n+eZdZ08FeFJ9qic2XZPq+DGG+706+sr5VNK+YxyQBTGJkBcCcMRTQWx9Eqixwpxq3rdWaLianjoHoO+t9Uhy8R3vCR+yTQq4iSLU4AB9ezgRwI7samwc3Fl2hSympuOJeQKBgQD9MKsBuebWJAMuWALd6xounPwBbw4JRgAEAGQSK6QT2bkviShv87Tj+KOfZF1G3jKnlTJ9zK+4L+5Efe2G4I205CBP/oDdhj9ryPkeAo1XcvRY+TpRtCE5ok6fYwOdg+xUAAcwOcCYn6ryodtFZgcmKyHScX1Uw8I+3f6bDUyhDQKBgQDhK7c28t+IFikYfE7yujixPIByXZG0a10u3RLvkba/LAUYPO4fP99Cec8ZTMys4N6PB4RDF/oq5rEg7Ivh1p1W1QlDLfKzrAmmF7HZeufMpYel7GKOYYtjMFlUqATIljQ5GOApE9YExCQj/ZIbcPimNgivRIIJ7xnIsGpQR+4ExwKBgQCx8b5+HBWscur5a0m4hj+EezhyGYcX5WalaVDrpFhQyzhnvFH7PKDpiBoXMAaOhCO9vBIcb/sfzC/9WzPRE4kElUBQeGJBTtTMABd1i/2idFxfG+ps+dhQFU6EnqYv0kQKVWq37h76JV2T4UWmZKqpnK7MpI6dQGgInyUN/C7bEQKBgGkUdZP3E8n2TYdXu6Aat4o+qdK1R+HimMHd+ZBDd/0PFfGRva5nqFDld3hfFsU2y6YD4/1LmzuaHN84hK8H8LUtBf2L289tYbNZK25KpRIjzYHpJrU7YEkRrx6KqnDRZ92ddj61OxNOVW9WTnDv75nhbXwdhHIOWwIFEhgU+UbRAoGBAMirP5hgLL5ALbULxJdDPes7632fCiFY9n78xg5kzoyG1lkwLhnBvyD9PNDl3RcQULKvaHvbfuwlPY2eek8cVQoF+WSspfpgI6HHqUqWGoEMoOcZiqlNS74Gy70U2QPWs4XJvTYff8CB6fFpgEc58HI4Y72JeENIn29QIwBtuZwy',
    ],

    'frontend_url' => [
        'local'      => 'http://localhost:3000/',
        'production' => 'https://dashboard.funnelliner.com/'
    ]

];