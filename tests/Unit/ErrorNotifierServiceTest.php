<?php

namespace ErrorNotifier\Notify\Tests\Unit;

use ErrorNotifier\Notify\Http\Services\ErrorNotifierService;
use ErrorNotifier\Notify\Jobs\SlackNotificationJob;
use ErrorNotifier\Notify\Notifications\NotifierNotification;
use ErrorNotifier\Notify\Tests\TestCase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class ErrorNotifierServiceTest extends TestCase
{
    public function test_instant_notification_is_skipped_when_disabled(): void
    {
        config([
            'notifier.instant' => false,
            'notifier.channels.mail.address' => 'admin@example.test',
        ]);
        Notification::fake();

        app(ErrorNotifierService::class)->sendInstantNotification(new RuntimeException('boom'));

        Notification::assertNothingSent();
    }

    public function test_instant_notification_is_skipped_and_logged_when_no_channel_is_configured(): void
    {
        config([
            'notifier.instant' => true,
            'notifier.channels.mail.address' => null,
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();
        Bus::fake();
        Log::spy();

        app(ErrorNotifierService::class)->sendInstantNotification(new RuntimeException('boom'));

        Log::shouldHaveReceived('warning')->once();
        Notification::assertNothingSent();
        Bus::assertNotDispatched(SlackNotificationJob::class);
    }

    public function test_instant_notification_emails_the_error_and_stores_it_in_session(): void
    {
        config([
            'notifier.instant' => true,
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();

        $exception = new RuntimeException('Something broke');
        app(ErrorNotifierService::class)->sendInstantNotification($exception);

        Notification::assertSentOnDemand(
            NotifierNotification::class,
            function ($notification, $channels, $notifiable) {
                $mail = $notification->toMail($notifiable);

                return $notifiable->routes['mail'] === ['admin@example.test']
                    && $mail->viewData['data']['message'] === 'Something broke';
            }
        );

        $this->assertSame('Something broke', session('error_notifier_package_message_123'));
        $this->assertSame(__FILE__, session('error_notifier_package_file_123'));
    }

    public function test_instant_notification_includes_three_trace_frames_by_default(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
        ]);
        Notification::fake();

        app(ErrorNotifierService::class)->sendInstantNotification($this->deeplyThrownException());

        Notification::assertSentOnDemand(
            NotifierNotification::class,
            fn ($notification, $channels, $notifiable) => count(json_decode(
                $notification->toMail($notifiable)->viewData['data']['trace'], true
            )) === 4 // the exception's own file/line, plus 3 trace frames.
        );
    }

    public function test_trace_frame_limit_is_configurable(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
            'notifier.trace_limit' => 1,
        ]);
        Notification::fake();

        app(ErrorNotifierService::class)->sendInstantNotification($this->deeplyThrownException());

        Notification::assertSentOnDemand(
            NotifierNotification::class,
            fn ($notification, $channels, $notifiable) => count(json_decode(
                $notification->toMail($notifiable)->viewData['data']['trace'], true
            )) === 2 // the exception's own file/line, plus 1 trace frame.
        );
    }

    public function test_trace_frame_limit_of_zero_reports_no_extra_frames(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
            'notifier.trace_limit' => 0,
        ]);
        Notification::fake();

        app(ErrorNotifierService::class)->sendInstantNotification($this->deeplyThrownException());

        Notification::assertSentOnDemand(
            NotifierNotification::class,
            fn ($notification, $channels, $notifiable) => count(json_decode(
                $notification->toMail($notifiable)->viewData['data']['trace'], true
            )) === 1 // just the exception's own file/line.
        );
    }

    private function deeplyThrownException(): \Throwable
    {
        try {
            $this->traceLevelOne();
        } catch (\Throwable $e) {
            return $e;
        }

        throw new \LogicException('Expected traceLevelOne() to throw.');
    }

    private function traceLevelOne(): void
    {
        $this->traceLevelTwo();
    }

    private function traceLevelTwo(): void
    {
        $this->traceLevelThree();
    }

    private function traceLevelThree(): void
    {
        throw new RuntimeException('deeply nested boom');
    }

    public function test_instant_notification_dispatches_a_slack_job_when_queueing_is_enabled(): void
    {
        config([
            'notifier.instant' => true,
            'notifier.channels.mail.address' => null,
            'notifier.channels.slack.url' => 'https://hooks.slack.test/x',
            'notifier.should_queue' => true,
        ]);
        Bus::fake();

        app(ErrorNotifierService::class)->sendInstantNotification(new RuntimeException('boom'));

        Bus::assertDispatched(SlackNotificationJob::class);
    }

    public function test_send_to_slack_dispatches_synchronously_when_queueing_is_disabled(): void
    {
        config(['notifier.should_queue' => false]);
        Bus::fake();

        app(ErrorNotifierService::class)->sendToSlack('boom', ['message' => 'boom']);

        Bus::assertDispatchedSync(SlackNotificationJob::class);
    }

    public function test_send_to_email_queues_the_notification_when_queueing_is_enabled(): void
    {
        config(['notifier.should_queue' => true]);
        Bus::fake();

        app(ErrorNotifierService::class)->sendToEmail(['admin@example.test'], ['message' => 'boom']);

        Bus::assertDispatched(SendQueuedNotifications::class);
    }

    public function test_send_to_email_sends_immediately_without_queueing_when_disabled(): void
    {
        config(['notifier.should_queue' => false]);
        Bus::fake();
        Mail::fake();

        app(ErrorNotifierService::class)->sendToEmail(['admin@example.test'], ['message' => 'boom']);

        Bus::assertNotDispatched(SendQueuedNotifications::class);
    }

    public function test_send_notification_ignores_client_submitted_trace_fields_and_uses_session_values(): void
    {
        config([
            'notifier.channels.mail.address' => 'admin@example.test',
            'notifier.channels.slack.url' => null,
            'notifier.redirect_url' => '/',
        ]);

        session([
            'error_notifier_package_message_123' => 'Original error',
            'error_notifier_package_data_123' => '[{"file":"real.php"}]',
            'error_notifier_package_file_123' => 'real.php',
            'error_notifier_package_line_123' => 42,
        ]);

        Notification::fake();

        app(ErrorNotifierService::class)->sendNotification([
            'message' => 'user description',
            'notifier_message' => 'spoofed message',
            'notifier_data' => 'spoofed trace',
            'notifier_line' => '999',
            'notifier_file' => '/etc/passwd',
        ]);

        Notification::assertSentOnDemand(
            NotifierNotification::class,
            function ($notification, $channels, $notifiable) {
                $data = $notification->toMail($notifiable)->viewData['data'];

                return $data['file'] === 'real.php'
                    && $data['line'] === 42
                    && $data['trace'] === '[{"file":"real.php"}]'
                    && $data['message'] === 'user description...Original error';
            }
        );

        $this->assertNull(session('error_notifier_package_message_123'));
        $this->assertNull(session('error_notifier_package_data_123'));
    }
}
