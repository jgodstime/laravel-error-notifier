<?php

return [

    'redirect_url' => env('NOTIFIER_REDIRECT_URL', '/'),
    'instant' => env('NOTIFIER_INSTANT', true),
    'name' => env('NOTIFIER_FROM_NAME', config('app.name')),

    'channels' => [

        'slack' => [
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
        ],

        'mail' => [
            'address' => env('NOTIFIER_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        ],

    ],


];
