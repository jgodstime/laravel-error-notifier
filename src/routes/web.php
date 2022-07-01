<?php

use Illuminate\Support\Facades\Route;
use ErrorNotifier\Notify\Http\Controllers\ErrorNotifierController;


// Route::get('/notifier', [ErrorNotifierController::class, 'index']);
Route::post('/notifier/send', [ErrorNotifierController::class, 'sendNotification'])->name('notifier.send');


