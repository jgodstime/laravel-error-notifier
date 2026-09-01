<?php

namespace ErrorNotifier\Notify\Tests\Unit;

use ErrorNotifier\Notify\Helper;
use ErrorNotifier\Notify\Notifications\NotifierNotification;
use ErrorNotifier\Notify\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class HelperTest extends TestCase
{
    public function test_get_error_triggers_an_instant_notification(): void
    {
        config([
            'notifier.instant' => true,
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();

        Helper::getError(new RuntimeException('boom'));

        Notification::assertSentOnDemand(NotifierNotification::class);
    }

    public function test_get_error_does_nothing_for_a_falsy_exception(): void
    {
        Notification::fake();

        Helper::getError(null);

        Notification::assertNothingSent();
    }

    public function test_get_error_reports_the_same_exception_instance_only_once(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();

        $exception = new RuntimeException('boom');
        Helper::getError($exception);
        Helper::getError($exception);

        Notification::assertSentOnDemandTimes(NotifierNotification::class, 1);
    }

    public function test_get_error_reports_two_distinct_exception_instances_separately(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();

        Helper::getError(new RuntimeException('boom'));
        Helper::getError(new RuntimeException('boom again'));

        Notification::assertSentOnDemandTimes(NotifierNotification::class, 2);
    }
}
