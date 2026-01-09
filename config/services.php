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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'whatsapp' => [
        // API Key for authentication
        'api_key' => env('WHATSAPP_API_KEY'),

        // Bearer token (fallback to API key)
        'bearer_token' => env('WHATSAPP_BEARER_TOKEN', env('WHATSAPP_API_KEY')),

        // Full API endpoint URL for sending messages
        'endpoint' => env('WHATSAPP_API_ENDPOINT'),

        // Phone Number ID from WABA Channels
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', '920609244473081'),

        // WABA Number
        'waba_number' => env('WHATSAPP_WABA_NUMBER', '919137315850'),

        // API Version (v23.0 per documentation)
        'api_version' => env('WHATSAPP_API_VERSION', 'v23.0'),

        // Default template name (working example uses campaign_message_v1)
        'template_name' => env('WHATSAPP_TEMPLATE_NAME', 'campaign_message_v1'),

        // Base URL (working curl example uses https)
        'base_url' => env('WHATSAPP_BASE_URL', 'https://meta.webpayservices.in'),

        // WABA ID for template management
        'waba_id' => env('WHATSAPP_WABA_ID', '735434666284028'),

        // Template management endpoint
        'template_endpoint' => env('WHATSAPP_TEMPLATE_ENDPOINT',
            'http://meta.webpayservices.in/{version}/{wabaId}/message_templates'),
    ],


];
