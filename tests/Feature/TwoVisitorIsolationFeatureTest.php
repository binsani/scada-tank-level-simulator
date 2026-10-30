<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Support\DemoStateKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Cookie\CookieValuePrefix;
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

        $this->app->forgetScopedInstances();
        $this->withCookie('scada_demo_visitor', $this->encryptedVisitorCookie($visitorA))
            ->getJson('/api/events')->assertOk();

        $this->assertSame($visitorA, app('request')->attributes->get('scada_demo_visitor_id'), 'First request must preserve supplied visitor cookie');

        $keyA = hash('sha256', DemoStateKey::forVisitor($visitorA));
        $keyB = hash('sha256', DemoStateKey::forVisitor($visitorB));

        Event::create(['type' => 'visitor-a', 'message' => 'A only', 'demo_visitor_key' => $keyA]);
        Event::create(['type' => 'visitor-b', 'message' => 'B only', 'demo_visitor_key' => $keyB]);
        Event::create(['type' => 'legacy', 'message' => 'Shared legacy record']);

        $this->app->forgetScopedInstances();
        $this->withCookie('scada_demo_visitor', $this->encryptedVisitorCookie($visitorA))
            ->getJson('/api/events')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'visitor-a');

        $this->app->forgetScopedInstances();
        $this->withCookie('scada_demo_visitor', $this->encryptedVisitorCookie($visitorB))
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

        $this->app->forgetScopedInstances();
        $this->withCookie('scada_demo_visitor', $this->encryptedVisitorCookie($visitorA))
            ->postJson('/api/start')->assertOk();

        $this->app->forgetScopedInstances();
        $this->withCookie('scada_demo_visitor', $this->encryptedVisitorCookie($visitorB))
            ->postJson('/api/reset')->assertOk();

        $this->assertSame($visitorB, app('request')->attributes->get('scada_demo_visitor_id'), 'Second request must preserve supplied visitor cookie');

        $stateA = Cache::get(DemoStateKey::forVisitor($visitorA));
        $stateB = Cache::get(DemoStateKey::forVisitor($visitorB));

        $this->assertNotNull($stateA);
        $this->assertNotNull($stateB);
        $this->assertNotEquals($stateA, $stateB);
    }
    private function encryptedVisitorCookie(string $visitorId): string
    {
        $name = 'scada_demo_visitor';
        $prefix = CookieValuePrefix::create($name, app('encrypter')->getKey());

        return encrypt($prefix.$visitorId, false);
    }
}
