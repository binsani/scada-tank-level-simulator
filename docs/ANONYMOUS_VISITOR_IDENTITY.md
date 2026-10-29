# Anonymous visitor identity: integration notes

This PR adds a **standalone identity resolver only**. It does not register middleware, send cookies, change the cache key used by SimulationEngine, or enable visitor isolation.

The resolver validates a server-generated 32-byte random identifier before using it and protects its cookie value with Laravel Crypt. On invalid cookie input it creates a new identity rather than accepting caller-selected cache keys.

## Required integration before enabling

- Register identity middleware on **both** dashboard/web and API endpoints. Laravel's API group is stateless by default.
- Account for Laravel's `EncryptCookies` middleware: web requests may already have decrypted cookie values, whereas API requests may not. Do not double-decrypt or double-encrypt; configure a single consistent cookie-handling strategy.
- Set cookie attributes: HttpOnly, Secure under HTTPS, SameSite=Lax, Path=/, and bounded expiry.
- Set a request-scoped identity, never a process-wide singleton identity.
- Scope all cache state, alarms, events, trends, reports, and mutation endpoints before activating the feature flag.
- Verify two independent browser sessions, forged cookies, cookie rotation, expiration, cross-route consistency, and CLI behavior with Laravel integration tests.

**Security warning:** The resolver is not ready to wire into production routes until cookie middleware behavior is reconciled and tested.
