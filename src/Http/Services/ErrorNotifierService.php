<?php

namespace ErrorNotifier\Notify\Http\Services;

use ErrorNotifier\Notify\Exceptions\EmailNotFound;
use ErrorNotifier\Notify\Jobs\SlackNotificationJob;
use ErrorNotifier\Notify\Notifications\NotifierNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ErrorNotifierService
{
    public function sendInstantNotification(\Throwable $e)
    {

        if (! config('notifier.instant')) {
            return;
        }

        if (! config('notifier.channels.slack.url') && ! config('notifier.channels.mail.address')) {
            Log::warning(EmailNotFound::make(null)->getMessage());

            return;
        }

        $errorLogs = collect([
            [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
            ],
        ])->concat(collect($e->getTrace())->take(config('notifier.trace_limit', 3)))->toArray();

        $data['is_authenticated'] = auth()->check() ? true : false;
        $data['id'] = auth()->check() ? auth()->id() : 'N/A';
        $data['email'] = auth()->check() ? (auth()->user()->email ?? 'N/A') : 'N/A';

        $data['status_code'] = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $data['access_url'] = url()->current();
        $data['message'] = $e->getMessage();

        $data['line'] = $e->getLine();
        $data['file'] = $e->getFile();
        $data['trace'] = json_encode($errorLogs);

        if (config('notifier.channels.mail.address')) {
            $emails = explode(',', config('notifier.channels.mail.address'));
            $this->sendToEmail($emails, $data);
        }

        if (config('notifier.channels.slack.url')) {
            $this->sendToSlack($e->getMessage(), $data);
        }

        session()->put('error_notifier_package_message_123', $e->getMessage());
        session()->put('error_notifier_package_data_123', json_encode($data));
        session()->put('error_notifier_package_file_123', $e->getFile());
        session()->put('error_notifier_package_line_123', $e->getLine());
    }

    public function sendToSlack($message, array $data)
    {
        if (config('notifier.should_queue')) {
            SlackNotificationJob::dispatch($data);
        } else {
            SlackNotificationJob::dispatchSync($data);
        }
    }

    public function sendToEmail(array $emails, array $data)
    {
        $notifiable = Notification::route('mail', $emails);
        $notification = new NotifierNotification($data);

        // NotifierNotification implements ShouldQueue, so ->notify() queues it
        // (falling back to sending inline if QUEUE_CONNECTION=sync). When
        // queueing is turned off we still want it sent, just not queued, so
        // sendNow() bypasses ShouldQueue entirely instead of dispatching it.
        if (config('notifier.should_queue')) {
            $notifiable->notify($notification);
        } else {
            Notification::sendNow($notifiable, $notification);
        }
    }

    public function sendNotification(array $data)
    {
        // Ignore any client-submitted notifier_* hidden fields and rely on the
        // server-side session values captured at the time the error occurred,
        // so a tampered form submission can't spoof the reported file/line/trace.
        unset($data['notifier_message'], $data['notifier_data'], $data['notifier_line'], $data['notifier_file']);

        $data['id'] = auth()->check() ? auth()->id() : 'N/A';
        $data['email'] = auth()->check() ? (auth()->user()->email ?? 'N/A') : 'N/A';
        $data['trace'] = session('error_notifier_package_data_123');
        $data['file'] = session('error_notifier_package_file_123');
        $data['line'] = session('error_notifier_package_line_123');
        $data['message'] = $data['message'].'...'.session('error_notifier_package_message_123');

        if (config('notifier.channels.mail.address')) {
            $emails = explode(',', config('notifier.channels.mail.address'));
            $this->sendToEmail($emails, $data);
        }

        if (config('notifier.channels.slack.url')) {
            $this->sendToSlack($data['message'], $data);
        }

        session()->forget([
            'error_notifier_package_message_123',
            'error_notifier_package_data_123',
            'error_notifier_package_line_123',
            'error_notifier_package_file_123',
        ]);

        return redirect(config('notifier.redirect_url'))->with('success', 'Thank you for your response, our tech team is on it');
    }
}
