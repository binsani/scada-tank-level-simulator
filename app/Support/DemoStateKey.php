<?php

namespace App\Support;

use InvalidArgumentException;

final class DemoStateKey
{
    public static function forVisitor(string $visitorId): string
    {
        // Accept only server-issued 32-byte identifiers represented as lowercase hex.
        if (! preg_match('/\\A[a-f0-9]{64}\\z/D', $visitorId)) {
            throw new InvalidArgumentException('Invalid demo visitor identifier.');
        }

        return 'scada.demo.'.hash('sha256', $visitorId).'.state';
    }

    public static function newVisitorId(): string
    {
        return bin2hex(random_bytes(32));
    }
}
