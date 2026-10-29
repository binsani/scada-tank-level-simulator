<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoVisitorIsolationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_demo_only_returns_legacy_events(): void
    {
        config(['demo.visitor_isolation_enabled' => false]);

        Event::create(['type' => 'legacy', 'message' => 'Legacy event']);
        Event::create(['type' => 'isolated', 'message' => 'Private event', 'demo_visitor_key' => str_repeat('a', 64)]);

        $this->getJson('/api/events')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'legacy');
    }

    public function test_isolated_visitors_do_not_see_legacy_events(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);

        Event::create(['type' => 'legacy', 'message' => 'Legacy event']);

        $this->getJson('/api/events')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_anonymous_controls_remain_available_in_legacy_mode(): void
    {
        config(['demo.visitor_isolation_enabled' => false]);

        $this->postJson('/api/reset')->assertOk();
        $this->postJson('/api/start')->assertOk();
        $this->postJson('/api/stop')->assertOk();
    }
}
