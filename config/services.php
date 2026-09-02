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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'order_sms' => [
        'webhook_url' => env('ORDER_SMS_WEBHOOK_URL'),
        'to' => env('ORDER_SMS_TO'),
    ],

    'rates' => [
        'source' => env('RATES_SOURCE', 'local'),
        'url' => env('RATES_API_URL'),
        'token' => env('RATES_API_TOKEN'),
        'host_header' => env('RATES_API_HOST_HEADER'),
        'verify_ssl' => env('RATES_API_VERIFY_SSL', true),
        'timeout' => env('RATES_API_TIMEOUT', 8),
        'cache_ttl' => env('RATES_API_CACHE_TTL', 10),
        'stale_ttl' => env('RATES_API_STALE_TTL', 300),
    ],

    'price_margin_sync' => [
        'url' => env('PRICE_MARGIN_SYNC_URL'),
        'token' => env('PRICE_MARGIN_SYNC_TOKEN'),
        'host_header' => env('PRICE_MARGIN_SYNC_HOST_HEADER'),
        'verify_ssl' => env('PRICE_MARGIN_SYNC_VERIFY_SSL', true),
        'timeout' => env('PRICE_MARGIN_SYNC_TIMEOUT', 10),
    ],

    'api_proxy' => [
        'base_url' => env('API_PROXY_BASE_URL'),
        'forwarded_host' => env('API_PROXY_FORWARDED_HOST', 'islamabadbullionexchange.com'),
        'verify_ssl' => env('API_PROXY_VERIFY_SSL', true),
        'timeout' => env('API_PROXY_TIMEOUT', 20),
    ],

];
