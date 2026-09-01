# Changelog

All notable changes to this project will be documented in this file.

## 3.0.0 - 2026-09-01

**Breaking:** minimum requirements raised to PHP 8.2 and Laravel 10 (previously PHP 7.3/8.0 and Laravel 8-10). If you're on an older stack, stay on the `1.x` release line.

- Restructured the package to the standard Laravel package layout (`config/`, `resources/`, `routes/` moved out of `src/`) and adopted `spatie/laravel-package-tools` for registration. No public API changes — the namespace, `Helper::getError()`, config keys, and routes are unchanged.
- Fixed a spoofable report: the feedback form's hidden `notifier_*` fields were trusted as-is, letting a submitter overwrite the reported file/line/trace with arbitrary text. The email/Slack report now always uses the server-side session values captured at the time of the original error.
- Added rate limiting (`throttle:10,1`) to the feedback form's submit route.
- Fixed `status_code` being read from `Exception::getCode()` (rarely an HTTP status) — now uses `getStatusCode()` when the exception provides one.
- Fixed the reportable exception's controller depending on the host app's `App\Http\Controllers\Controller`; it now extends `Illuminate\Routing\Controller` directly.
- Explicitly wrapped the package's routes in the `web` middleware group instead of relying on it being applied incidentally.
- Added a test suite (Pest + Orchestra Testbench), Pint, and Larastan/PHPStan, plus a GitHub Actions workflow testing PHP 8.2–8.4 against Laravel 10–13.
- The package now registers itself on the app's exception handler automatically (`reportable()`), so manually editing `app/Exceptions/Handler.php` is no longer required — this also fixes installs on Laravel 11+, which doesn't generate that file at all. Calling `Helper::getError()` yourself still works and is now safe to combine with auto-reporting (the same exception instance is never reported twice); set `NOTIFIER_AUTO_REPORT=false` to opt out of auto-reporting entirely.
- Fixed email notifications never actually being queued: `NotifierNotification` used the `Queueable` trait but never implemented `ShouldQueue`, so `notifier.should_queue` had no effect on the mail channel (Slack was unaffected — its job already implemented `ShouldQueue` correctly). Queueing now behaves the same way for both channels.
- Redesigned the notification email (`resources/views/emails/index.blade.php`) from a generic, unstyled template into a proper bug-report layout: status badge, a readable metadata grid (file, line, timestamp, auth state, user), and a formatted stack trace instead of a raw JSON dump. Same `$data` contract — no changes needed to code that sends the notification. Light mode is the default; dark mode only applies via `prefers-color-scheme`, and `color-scheme`/`supported-color-schemes` meta tags stop dark-mode-aware clients (Outlook.com, Windows Mail, some Gmail contexts) from auto-inverting the light design on their own.
- The number of stack trace frames included in a report (previously hardcoded to 3) is now configurable via `NOTIFIER_TRACE_LIMIT` (`notifier.trace_limit` in config), still defaulting to 3.
- Redesigned the optional 500-error feedback page (`resources/views/500.blade.php`) to match the new email's visual language. Same form `action` and hidden inputs as before — restyling it further is safe as long as those stay intact.
- Rewrote the README to lead with the fact that this package works in any Laravel app (API-only included) with no required setup — the Blade feedback page is now clearly marked as an opt-in extra for apps that render views, rather than implied as a required step.
- Fixed the CI matrix (all `prefer-lowest` cells and several `prefer-stable` ones were failing): added `guzzlehttp/guzzle` and `guzzlehttp/promises` as direct dependencies (the package calls `Http::post()` but never required Guzzle itself, so the lowest resolvable dependency set didn't always include it), disabled Composer's security-advisory install-blocking policy (`config.policy: false`) which was rejecting old-but-otherwise-valid `laravel/framework` patch releases during `--prefer-lowest`, and fixed a real type mismatch in `ErrorNotifierService` (concatenating two differently-shaped array collections) that Larastan only caught on an older PHPStan version.
- Fixed flaky `Log::shouldReceive(...)->once()` tests under PHP 8.4: old vendor code (`Illuminate\Log\Logger`, an Orchestra exception class) triggers implicit-nullable-parameter deprecation notices on PHP 8.4, which the framework funnels through the logger — colliding with the strict partial mock and failing with "no expectations were specified" for the framework's own incidental `Log::channel()` call. Switched to `Log::spy()` + `Log::shouldHaveReceived(...)`, which tolerates calls it wasn't told to expect.

## 1.1.2 and earlier

See the git history for changes prior to this changelog.
