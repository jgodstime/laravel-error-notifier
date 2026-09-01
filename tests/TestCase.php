<?php

namespace ErrorNotifier\Notify\Tests;

use ErrorNotifier\Notify\ErrorNotifierServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [ErrorNotifierServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('session.driver', 'array');
        $app['config']->set('notifier.channels.mail.address', null);
        $app['config']->set('notifier.channels.slack.url', null);
    }
}
