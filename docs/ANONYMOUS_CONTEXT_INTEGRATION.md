# Anonymous visitor context integration notes

The `IdentifyDemoVisitor` middleware and `DemoVisitorContext` helper are **not wired into routes** yet. The visitor isolation flag remains disabled.

## Before enabling

- Laravel's web middleware already handles cookie encryption. Avoid encrypting a cookie twice or relying on a web-only decrypted value for stateless API routes.
- Establish one consistent, authenticated cookie handling strategy for both API and web routes, with explicit middleware ordering and end-to-end tests.
- Ensure the application has a valid `APP_KEY`; HTTPS deployments must set secure cookie behavior correctly behind trusted proxies.
- Add request tests proving that the same browser maintains one visitor identity across dashboard and API, and forged cookies rotate.
- Only after that, scope the simulation engine, event logs, alarms, trends, reports and background tick behavior.
- The current helper is scaffolding; do not register it in production middleware or enable the feature flag until integration is complete.

No changes in this branch alter active routes or simulation behavior.
