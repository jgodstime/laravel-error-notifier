<?php

namespace ErrorNotifier\Notify\Tests\Unit;

use ErrorNotifier\Notify\Jobs\SlackNotificationJob;
use ErrorNotifier\Notify\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationJobTest extends TestCase
{
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'is_authenticated' => true,
            'message' => 'Something broke',
            'file' => '/app/Foo.php',
            'line' => 10,
            'access_url' => 'https://app.test/page',
            'id' => 1,
            'email' => 'user@example.test',
            'trace' => '[]',
        ], $overrides);
    }

    public function test_it_posts_the_error_details_to_the_configured_webhook(): void
    {
        config(['notifier.channels.slack.url' => 'https://hooks.slack.test/x']);
        Http::fake(['hooks.slack.test/*' => Http::response(['ok' => true], 200)]);

        (new SlackNotificationJob($this->payload()))->handle();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.slack.test/x'
                && str_contains(json_encode($request->data()), 'Something broke');
        });
    }

    public function test_it_logs_when_the_webhook_request_fails(): void
    {
        config(['notifier.channels.slack.url' => 'https://hooks.slack.test/x']);
        Http::fake(['hooks.slack.test/*' => Http::response('nope', 500)]);
        Log::spy();

        (new SlackNotificationJob($this->payload()))->handle();

        Log::shouldHaveReceived('error')->once();
    }
}
