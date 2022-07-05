<?php
namespace ErrorNotifier\Notify\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
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


            $data['is_authenticated'] = auth()->check() ? 'yes' : 'no';
            if(auth()->check()){
                $data['id'] = auth()->check() ? auth()->id() : null;
                $data['email'] = auth()->check() ? @auth()->user()->email : null;
            }

            $data['status_code'] =  $e->getCode();
            $data['access_url'] = url()->current();
            $data['message'] = $e->getMessage();
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


    }


    public function sendToSlack($message, array $data)
    {
        Log::channel('slack')->error($message, $data);

    }


    public function sendToEmail($emails, array $data)
    {
        foreach ($emails as $key => $email) {
            (new User)->forceFill([
                'name' => config('app.name'),
                'email' => $email,
            ])->notify(new NotifierNotification($data));
        }

    }


    public function sendNotification(array $data)
    {

        if(!$data['is_authenticated'] == '1'){
            unset($data['id']);
            unset($data['email']);
        }

        $data['trace'] = $data['notifier_data'];
        $data['message'] = $data['message']. '...'.$data['notifier_message'];

        if(config('notifier.channels.mail.address')){
            $emails = explode(',', config('notifier.channels.mail.address'));
            $this->sendToEmail($emails, $data);
        }


        if(config('notifier.channels.slack.url')){
            unset($data['notifier_message']);
            unset($data['notifier_data']);
            $message = $data['message'];
            unset($data['message']);
            $this->sendToSlack($message, $data);
        }

        session()->forget('error_notifier_package_message_123');
        session()->forget('error_notifier_package_data_123');

        return redirect(config('notifier.redirect_url'))->with('success',  'Thank you for your response, our tech team is on it');

    }


}
