<?php
namespace ErrorNotifier\Notify\Http\Services;

use App\Models\User;
use ErrorNotifier\Notify\Exceptions\EmailNotFound;
use ErrorNotifier\Notify\Notifications\NotifierNotification;

class ErrorNotifierService{

    public function sendNotification(array $data)
    {
        if(!config('notifier.email')){
            throw EmailNotFound::make(null);
        }

        $emails = explode(',', config('notifier.email'));

        foreach ($emails as $key => $email) {
            (new User)->forceFill([
                'name' => config('app.name'),
                'email' => $email,
            ])->notify(new NotifierNotification($data));
        }

        return redirect(config('notifier.redirect_url'))->with('success', 'Thank you for your response, our tech team is on it');

    }
}
