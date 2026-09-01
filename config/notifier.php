<?php

return [

    'redirect_url' => env('NOTIFIER_REDIRECT_URL', '/'),
    'instant' => env('NOTIFIER_INSTANT', true),

    // When true, the package registers its own `reportable()` callback on the
    // app's exception handler — no need to call Helper::getError() yourself,
    // which matters on Laravel 11+ where there's no app/Exceptions/Handler.php
    // to add it to. Set to false if you'd rather call Helper::getError()
    // yourself (e.g. to only report a subset of exceptions).
    'auto_report' => env('NOTIFIER_AUTO_REPORT', true),
    'name' => env('NOTIFIER_FROM_NAME', config('app.name')),
    'should_queue' => env('NOTIFIER_SHOULD_QUEUE', true),

    // How many stack frames (beyond the exception's own file/line) to include
    // in the reported trace.
    'trace_limit' => env('NOTIFIER_TRACE_LIMIT', 3),

    'channels' => [

        'slack' => [
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
        ],

        'mail' => [
            'address' => env('NOTIFIER_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        ],

    ],

];
