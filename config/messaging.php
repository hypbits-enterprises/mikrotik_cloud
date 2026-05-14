<?php

return [

    'whatsapp' => [
        'phone_number_id'       => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id'   => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'access_token'          => env('WHATSAPP_ACCESS_TOKEN'),
        'webhook_verify_token'  => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'api_version'           => env('WHATSAPP_API_VERSION', 'v19.0'),
        'base_url'              => 'https://graph.facebook.com',
    ],

    'message_categories' => [
        'service'        => 'Service',
        'utility'        => 'Utility',
        'authentication' => 'Authentication',
        'marketing'      => 'Marketing',
    ],

    'max_templates' => 3,

];
