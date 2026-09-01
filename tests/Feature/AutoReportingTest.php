<?php

namespace ErrorNotifier\Notify\Tests\Feature;

use ErrorNotifier\Notify\Helper;
use ErrorNotifier\Notify\Notifications\NotifierNotification;
use ErrorNotifier\Notify\Tests\TestCase;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class AutoReportingTest extends TestCase
{
    public function test_reporting_an_exception_through_the_handler_triggers_a_notification(): void
    {
        config([
            'notifier.auto_report' => true,
            'notifier.channels.mail.address' => 'admin@example.test',
        ]);
        Notification::fake();

        $this->app->make(ExceptionHandler::class)->report(new RuntimeException('boom'));

        Notification::assertSentOnDemand(NotifierNotification::class);
    }

    public function test_auto_reporting_can_be_disabled(): void
    {
        config([
            'notifier.auto_report' => false,
            'notifier.channels.mail.address' => 'admin@example.test',
        ]);
        Notification::fake();

        $this->app->make(ExceptionHandler::class)->report(new RuntimeException('boom'));

        Notification::assertNothingSent();
    }

    public function test_the_same_exception_is_not_reported_twice_when_also_reported_manually(): void
    {
        config([
            'notifier.auto_report' => true,
            'notifier.channels.mail.address' => 'admin@example.test',
        ]);
        Notification::fake();

        $exception = new RuntimeException('boom');

        // Simulates a project that still has the manual Helper::getError()
        // call left over in its own exception handler alongside auto-reporting.
        Helper::getError($exception);
        $this->app->make(ExceptionHandler::class)->report($exception);

        Notification::assertSentOnDemandTimes(NotifierNotification::class, 1);
    }
}
