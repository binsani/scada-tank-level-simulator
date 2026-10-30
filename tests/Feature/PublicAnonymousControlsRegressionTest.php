<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicAnonymousControlsRegressionTest extends TestCase
{
    public function test_simulation_controls_remain_public_without_authentication(): void
    {
        config(['demo.visitor_isolation_enabled' => false]);

        foreach (['start', 'stop', 'reset', 'manual', 'auto'] as $action) {
            $response = $this->postJson('/api/'.$action);

            $response->assertOk();
            $this->assertNotSame(401, $response->status());
            $this->assertNotSame(403, $response->status());
        }
    }

    public function test_visitor_isolation_disabled_does_not_issue_identity_cookie(): void
    {
        config(['demo.visitor_isolation_enabled' => false]);

        $response = $this->getJson('/api/tank');

        $response->assertOk();
        $this->assertNull($response->headers->getCookies()[0] ?? null);
    }
}
