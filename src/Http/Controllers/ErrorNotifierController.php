<?php

namespace ErrorNotifier\Notify\Http\Controllers;

use ErrorNotifier\Notify\Http\Requests\ErrorNotifierRequest;
use ErrorNotifier\Notify\Http\Services\ErrorNotifierService;
use Illuminate\Routing\Controller;

class ErrorNotifierController extends Controller
{
    protected $errorNotifierService;

    public function __construct(ErrorNotifierService $errorNotifierService)
    {
        $this->errorNotifierService = $errorNotifierService;
    }

    public function index($statusCode = 500)
    {
        return view("notifier::{$statusCode}");
    }

    public function sendNotification(ErrorNotifierRequest $request)
    {
        return $this->errorNotifierService->sendNotification($request->validated());
    }
}
