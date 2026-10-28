<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Cookie;

final class DemoVisitorContext
{
    public const COOKIE_NAME = 'scada_demo_visitor';

    public static function fromRequest(Request $request): array
    {
        $incoming = $request->cookie(self::COOKIE_NAME);
        $id = null;

        if (is_string($incoming)) {
            try {
                $decrypted = Crypt::decryptString($incoming);
                if (preg_match('/\\A[a-f0-9]{64}\\z/D', $decrypted) === 1) {
                    $id = $decrypted;
                }
            } catch (DecryptException $exception) {
                // Forged, stale or corrupted cookies are replaced.
            }
        }

        $isNew = $id === null;
        $id ??= DemoStateKey::newVisitorId();

        return [
            'id' => $id,
            'new' => $isNew,
            'state_key' => DemoStateKey::forVisitor($id),
        ];
    }

    public static function cookie(string $visitorId, Request $request): Cookie
    {
        return Cookie::create(
            self::COOKIE_NAME,
            Crypt::encryptString($visitorId),
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
