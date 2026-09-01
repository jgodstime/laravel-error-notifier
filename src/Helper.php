<?php

namespace ErrorNotifier\Notify;

use ErrorNotifier\Notify\Http\Services\ErrorNotifierService;
use Throwable;
use WeakMap;

class Helper
{
    // Keyed by the exception object itself (not spl_object_id, which Laravel
    // can silently reuse for an unrelated exception once the original is
    // garbage collected in a long-running worker) so the same exception isn't
    // reported twice when both the package's auto-registered reportable()
    // callback and a manually added Helper::getError() call see it.
    private static ?WeakMap $reported = null;

    public static function getError($e)
    {
        if (! $e instanceof Throwable) {
            return;
        }

        self::$reported ??= new WeakMap;

        if (isset(self::$reported[$e])) {
            return;
        }

        self::$reported[$e] = true;

        app(ErrorNotifierService::class)->sendInstantNotification($e);
    }
}
