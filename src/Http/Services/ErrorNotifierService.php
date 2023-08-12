<?php
namespace ErrorNotifier\Notify\Http\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use ErrorNotifier\Notify\Jobs\SlackNotificationJob;
use ErrorNotifier\Notify\Notifications\NotifierNotification;

class ErrorNotifierService{

    public function sendInstantNotification(object $e)
    {

        if(!config('notifier.instant') == true){
            return ;
        }

        if(config('notifier.channels.slack.url') || config('notifier.channels.mail.address')){
            $errorLogs = collect([
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                ]
            ])->concat(collect($e->getTrace())->take(3))->toArray();


            $data['is_authenticated'] = auth()->check() ? true : false;
            $data['id'] = auth()->check() ? auth()->id() : "N/A";
            $data['email'] = auth()->check() ? @auth()->user()->email : "N/A";

            $data['status_code'] =  $e->getCode();
            $data['access_url'] = url()->current();
            $data['message'] = $e->getMessage();

            $data['line'] = $e->getLine();
            $data['file'] = $e->getFile();
            $data['trace'] = json_encode($errorLogs);
        }

        if(config('notifier.channels.mail.address')){
            $emails = explode(',', config('notifier.channels.mail.address'));
            $this->sendToEmail($emails, $data);
        }


        if(config('notifier.channels.slack.url')){
            $this->sendToSlack($e->getMessage(), $data);
        }

        session()->put('error_notifier_package_message_123', $e->getMessage());
        session()->put('error_notifier_package_data_123', json_encode($data));
        session()->put('error_notifier_package_file_123',  $e->getFile());
        session()->put('error_notifier_package_line_123',  $e->getLine());

    }


    public function sendToSlack($message, array $data)
    {
        if(config('notifier.should_queue')){
            SlackNotificationJob::dispatch($data);
        }else{
            SlackNotificationJob::dispatchSync($data);
        }
    }


    public function sendToEmail(array $emails, array $data)
    {
        Notification::route('mail', $emails)->notify(new NotifierNotification($data));
    }


    public function sendNotification(array $data)
    {

        $data['id'] = auth()->check() ? auth()->id() : "N/A";
        $data['email'] = auth()->check() ? @auth()->user()->email : "N/A";
        $data['trace'] = $data['notifier_data'];
        $data['line'] = $data['notifier_line'];
        $data['file'] = $data['notifier_file'];
        $data['message'] = $data['message']. '...'.$data['notifier_message'];

        if(config('notifier.channels.mail.address')){
            $emails = explode(',', config('notifier.channels.mail.address'));
            $this->sendToEmail($emails, $data);
        }

        if(config('notifier.channels.slack.url')){
            unset($data['notifier_message']);
            unset($data['notifier_data']);
            unset($data['notifier_line']);
            unset($data['notifier_file']);
            $message = $data['message'];
            $this->sendToSlack($message, $data);
        }

        session()->forget('error_notifier_package_message_123');
        session()->forget('error_notifier_package_data_123');
        session()->forget('error_notifier_package_line_123');
        session()->forget('error_notifier_package_file_123');

        return redirect(config('notifier.redirect_url'))->with('success',  'Thank you for your response, our tech team is on it');
    }


}
