<?php

namespace ErrorNotifier\Notify\Tests\Feature;

use ErrorNotifier\Notify\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class ErrorNotifierRoutesTest extends TestCase
{
    public function test_the_notifier_page_renders(): void
    {
        $this->get('/notifier')->assertOk();
    }

    public function test_submitting_without_a_message_fails_validation(): void
    {
        $this->from('/notifier')
            ->post('/notifier/send', [])
            ->assertSessionHasErrors('message');
    }

    public function test_submitting_a_valid_message_redirects_with_a_success_message(): void
    {
        Notification::fake();

        $this->post('/notifier/send', ['message' => 'It broke when I clicked save'])
            ->assertRedirect('/')
            ->assertSessionHas('success');
    }

    public function test_the_send_route_is_throttled(): void
    {
        $route = collect($this->app['router']->getRoutes())
            ->first(fn ($route) => $route->getName() === 'notifier.send');

        $this->assertNotNull($route);
        $this->assertContains('throttle:10,1', $route->middleware());
    }
}
