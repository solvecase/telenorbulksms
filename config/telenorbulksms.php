<?php

return [
    'base_url' => env('TELENOR_API_BASE_URL', 'http://sandbox-apigw.mytelenor.com.mm/'),
    
    'sms' => [
        'callback_url' => env('TELENOR_SMS_CALLBACK_URL', 'oauth2/telenorsms/callback'),
        'client_id' => env('TELENOR_SMS_CLIENT_ID', ''),
        'client_secret' => env('TELENOR_SMS_CLIENT_SECRET', ''),
        'sender' => env('TELENOR_SMS_SENDER', ''),
        'username' => env('TELENOR_SMS_USERNAME', ''),
        'password' => env('TELENOR_SMS_PASSWORD', '')
    ]
];