<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

final class DemoVisitorIdentity
{
    public const COOKIE_NAME = 'scada_demo_visitor';

    public function resolve(Request $request): array
    {
        $cookie = $request->cookie(self::COOKIE_NAME);

        if (is_string($cookie)) {
            try {
                $id = Crypt::decryptString($cookie);
                // Only accept a 32-byte random identifier, never arbitrary keys.
                DemoStateKey::forVisitor($id);

                return [$id, false];
            } catch (\Throwable $e) {
                // Invalid or expired/replaced app-key cookie: issue a new identity.
            }
        }

        return [DemoStateKey::newVisitorId(), true];
    }

    public function encryptedCookieValue(string $id): string
    {
        DemoStateKey::forVisitor($id);

        return Crypt::encryptString($id);
    }
}
