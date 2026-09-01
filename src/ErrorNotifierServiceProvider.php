<?php

namespace ErrorNotifier\Notify;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class ErrorNotifierServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('notifier')
            ->hasConfigFile()
            ->hasViews('notifier')
            ->hasRoute('web');
    }

    public function packageBooted(): void
    {
        // Laravel renders resources/views/errors/500.blade.php for any uncaught
        // exception, regardless of how it was raised — this has to land there,
        // separately from the package's own "notifier::" view namespace, or the
        // feedback page never actually replaces the framework's default one.
        $this->publishes([
            $this->package->basePath('/../resources/views/500.blade.php') => resource_path('views/errors/500.blade.php'),
        ], 'notifier-error-view');

        $this->registerAutoReporting();
    }

    /**
     * Laravel 11+ apps have no app/Exceptions/Handler.php to add a reportable()
     * call to — bootstrap/app.php's withExceptions() just configures the same
     * underlying handler instance instead. Rather than document two different
     * setup steps per Laravel version, hook that instance directly so the
     * package works with zero manual wiring on every supported version.
     * Helper::getError() de-dupes by exception instance, so this is safe to
     * leave on even if a project also still calls it manually.
     */
    protected function registerAutoReporting(): void
    {
        $handler = $this->app->make(ExceptionHandler::class);

        if (! method_exists($handler, 'reportable')) {
            return;
        }

        $handler->reportable(function (Throwable $e) {
            if (config('notifier.auto_report')) {
                Helper::getError($e);
            }
        });
    }
}
