<?php

use ErrorNotifier\Notify\Http\Controllers\ErrorNotifierController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/notifier', [ErrorNotifierController::class, 'index']);

    Route::post('/notifier/send', [ErrorNotifierController::class, 'sendNotification'])
        ->middleware('throttle:10,1')
        ->name('notifier.send');
});
