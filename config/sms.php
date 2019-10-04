<?php

return [
    
    'default' => env('SMS_CHANNEL', 'nusasms'),

    'channels' => [

        // 'name' => [
        //     'keys' => [
        //         'message' => 'message',
        //         'phone' => 'phone',
        //     ],
        //     'payloads' => [
        //         //
        //     ],
        //     'payload_type' => 'form_params',
        //     'headers' => [
        //         //
        //     ],
        //     'credentials' => [
        //         //
        //     ],
        //     'request_method' => 'POST',
        //     'api_url' => null,
        //     'bulk' => false,
        // ],

        'nusasms' => [
            'api_url' => env('SMS_NUSA_API_URL', 'http://api.nusasms.com/api/v3/sendsms/plain'),
            'keys' => [
                'message' => 'SMSText',
                'phone' => 'GSM',
            ],
            'payloads' => [
                'output' => 'json',
            ],
            'credentials' => [
                'user' => env('SMS_NUSA_AUTH_USER', null),
                'password' => env('SMS_NUSA_AUTH_PASSWORD', null),
            ],
        ],

        'zenziva' => [
            'api_url' => env('SMS_ZENZIVA_API_URL', 'https://reguler.zenziva.net/apps/smsapi.php'),
            'keys' => [
                'message' => 'pesan',
                'phone' => 'nohp',
            ],
            'request_method' => 'GET',
            'credentials' => [
                'userkey' => env('SMS_ZENZIVA_AUTH_USER', null),
                'passwordkey' => env('SMS_ZENZIVA_AUTH_PASSWORD', null),
            ],
        ],

        'smsgatewaydotme' => [
            'api_url' => env('SMS_SMSGATEWAYME_API_URL', 'https://smsgateway.me/api/v4/message/send'),
            'keys' => [
                'message' => 'message',
                'phone' => 'phone_number',
            ],
            'headers' => [
                'Authorization' => env('SMS_SMSGATEWAYME_AUTH_TOKEN', null),
            ],
            'bulk' => true,
            'payloads' => [
                'device_id' => env('SMS_SMSGATEWAYME_DEFAULT_DEVICE_ID', null),
            ],
            'payload_type' => 'json',
        ],
    ],
];
