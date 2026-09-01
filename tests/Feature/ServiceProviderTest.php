<?php

namespace ErrorNotifier\Notify\Tests\Feature;

use ErrorNotifier\Notify\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_the_package_config_is_registered(): void
    {
        $this->assertSame('/', config('notifier.redirect_url'));
        $this->assertTrue(config('notifier.instant'));
    }

    public function test_the_package_routes_are_registered(): void
    {
        $this->assertTrue($this->app['router']->has('notifier.send'));
    }

    public function test_the_notification_email_view_renders(): void
    {
        $html = view('notifier::emails.index', [
            'data' => [
                'message' => 'Something broke',
                'file' => '/app/Foo.php',
                'line' => 10,
                'access_url' => 'https://app.test/page',
                'is_authenticated' => true,
                'id' => 1,
                'email' => 'user@example.test',
                'trace' => '[]',
            ],
        ])->render();

        $this->assertStringContainsString('Something broke', $html);
    }
}
