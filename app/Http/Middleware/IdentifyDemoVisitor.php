<?php

namespace App\Http\Middleware;

use App\Support\DemoStateKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\HttpFoundation\Response;

class IdentifyDemoVisitor
{
    private const COOKIE = 'scada_demo_visitor';

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.visitor_isolation_enabled', false)) {
            return $next($request);
        }

        $id = null;
        $encoded = $request->cookie(self::COOKIE);

        if (is_string($encoded)) {
            try {
                $candidate = Crypt::decryptString($encoded);
                // Validate identifiers before using them as a state namespace.
                DemoStateKey::forVisitor($candidate);
                $id = $candidate;
            } catch (DecryptException|\InvalidArgumentException $exception) {
                // Invalid or expired cookies start a fresh anonymous demo.
            }
        }

        $newVisitor = $id === null;
        $id ??= DemoStateKey::newVisitorId();
        $request->attributes->set('scada_demo_visitor_id', $id);

        $response = $next($request);

        if ($newVisitor) {
            $ttl = max(1, (int) config('demo.visitor_ttl_minutes', 120));
            $response->headers->setCookie(cookie(
                self::COOKIE,
                Crypt::encryptString($id),
                $ttl,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax',
            ));
        }

        return $response;
    }
}
