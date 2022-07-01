<?php

return [

    'redirect_url' => env('NOTIFIER_REDIRECT_URL', '/'),
    'email' => env('NOTIFIER_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
    
];
