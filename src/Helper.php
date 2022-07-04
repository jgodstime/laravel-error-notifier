<?php
namespace ErrorNotifier\Notify;

use Illuminate\Support\Facades\Session;

class Helper{

    public static function getError($e)
    {
        if($e){
           app(\ErrorNotifier\Notify\Http\Services\ErrorNotifierService::class)->sendInstantNotification($e);
        }
    }


}

