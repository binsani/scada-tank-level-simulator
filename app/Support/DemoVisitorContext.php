<?php

namespace App\Support;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

final class DemoVisitorContext
{
    public const COOKIE_NAME = 'scada_demo_visitor';

    public static function fromRequest(Request $request): array
    {
        $incoming = $request->cookie(self::COOKIE_NAME);
        $valid = is_string($incoming) && preg_match('/\\A[a-f0-9]{64}\\z/D', $incoming) === 1;
        $id = $valid ? $incoming : DemoStateKey::newVisitorId();

        return [
            'id' => $id,
            'new' => ! $valid,
            'state_key' => DemoStateKey::forVisitor($id),
        ];
    }

    public static function cookie(string $visitorId, Request $request): Cookie
    {
        // Cookie is HttpOnly; Secure is enforced for HTTPS requests.
        return Cookie::create(
            self::COOKIE_NAME,
            $visitorId,
            now()->addMinutes((int) config('demo.visitor_ttl_minutes', 120)),
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax'
        );
    }
}
