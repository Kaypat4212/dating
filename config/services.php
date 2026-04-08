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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'agora' => [
        'app_id'          => env('AGORA_APP_ID', ''),
        'app_certificate' => env('AGORA_APP_CERTIFICATE', ''),
    ],

    // Daily.co — free 10K participant-mins/month
    // Sign up: https://dashboard.daily.co → grab your API key + domain
    // Falls back to Jitsi Meet (100% free, no account) if not configured.
    'dailyco' => [
        'api_key' => env('DAILY_CO_API_KEY', ''),
        'domain'  => env('DAILY_CO_DOMAIN', ''), // e.g. 'heartsconnect'
    ],

    'iphub' => [
        'api_key' => env('IPHUB_API_KEY'),
        'enabled' => env('VPN_ENABLE_IPHUB', true),
    ],

    'proxycheck' => [
        'api_key' => env('PROXYCHECK_API_KEY'),
        'enabled' => env('VPN_ENABLE_PROXYCHECK', true),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', false),
    ],
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

];
