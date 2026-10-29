<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Support\DemoStateKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TwoVisitorIsolationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_anonymous_visitors_only_see_their_own_events(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);

        $visitorA = DemoStateKey::newVisitorId();
        $visitorB = DemoStateKey::newVisitorId();

        $this->withCookie('scada_demo_visitor', encrypt($visitorA))
            ->getJson('/api/events')->assertOk();

        $keyA = hash('sha256', DemoStateKey::forVisitor($visitorA));
        $keyB = hash('sha256', DemoStateKey::forVisitor($visitorB));

        Event::create(['type' => 'visitor-a', 'message' => 'A only', 'demo_visitor_key' => $keyA]);
        Event::create(['type' => 'visitor-b', 'message' => 'B only', 'demo_visitor_key' => $keyB]);
        Event::create(['type' => 'legacy', 'message' => 'Shared legacy record']);

        $this->withCookie('scada_demo_visitor', encrypt($visitorA))
            ->getJson('/api/events')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'visitor-a');

        $this->withCookie('scada_demo_visitor', encrypt($visitorB))
            ->getJson('/api/events')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'visitor-b');
    }

    public function test_two_anonymous_visitors_have_independent_simulation_state(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);
        Cache::clear();

        $visitorA = DemoStateKey::newVisitorId();
        $visitorB = DemoStateKey::newVisitorId();

        $this->withCookie('scada_demo_visitor', encrypt($visitorA))
            ->postJson('/api/start')->assertOk();

        $this->withCookie('scada_demo_visitor', encrypt($visitorB))
            ->postJson('/api/reset')->assertOk();

        $stateA = Cache::get(DemoStateKey::forVisitor($visitorA));
        $stateB = Cache::get(DemoStateKey::forVisitor($visitorB));

        $this->assertNotNull($stateA);
        $this->assertNotNull($stateB);
        $this->assertNotEquals($stateA, $stateB);
    }
}
