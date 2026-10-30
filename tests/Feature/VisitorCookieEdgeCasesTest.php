<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisitorCookieEdgeCasesTest extends TestCase
{
    public function test_missing_cookie_issues_new_anonymous_identity_without_login(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);

        $response = $this->withCredentials()->getJson('/api/tank');

        $response->assertOk();
        $id = app('request')->attributes->get('scada_demo_visitor_id');
        $this->assertIsString($id);
        $this->assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/D', $id);
        $this->assertNotNull($response->headers->getCookies()[0] ?? null);
    }

    public function test_invalid_cookie_is_replaced_instead_of_using_shared_state(): void
    {
        config(['demo.visitor_isolation_enabled' => true]);

        $response = $this->withCookie('scada_demo_visitor', 'invalid-visitor-id')
            ->withCredentials()->getJson('/api/tank');

        $response->assertOk();
        $id = app('request')->attributes->get('scada_demo_visitor_id');
        $this->assertIsString($id);
        $this->assertNotSame('invalid-visitor-id', $id);
        $this->assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/D', $id);
        $this->assertNotNull($response->headers->getCookies()[0] ?? null);
    }
}
