# Visitor-isolated public demo: implementation plan

## Current behavior

The simulation engine stores one shared state at `scada.simulation.state` in Laravel Cache. All visitors operate that same state. The `scada:tick` command also advances the shared state. EventLogger and TrendRecorder write shared records, so isolating the cache key alone would **not** isolate all user-visible information.

## Goal

Keep the public demo accessible without authentication while giving each visitor a separate, temporary simulation. No visitor should be able to reset another visitor's tank. The simulator must never control real equipment.

## Proposed architecture

1. Use a server-issued, random, opaque demo identifier stored in a Secure, HttpOnly, SameSite=Lax cookie. Never accept arbitrary cache keys or user-supplied identifiers.
2. Introduce a dedicated demo-session middleware for both web dashboard and API routes. Laravel's `api` middleware group is stateless by default, so do not assume `session()` or web-session cookies work there. If using Laravel sessions instead of a custom signed cookie, configure the required cookie/session middleware consistently for both route groups.
3. Derive a namespaced cache key from the validated identifier, e.g. `scada.demo.{id}.state`, with a bounded TTL. Rotate the identifier when necessary. Never return it in API payloads or logs.
4. Refactor SimulationEngine to resolve the current demo context per request, rather than capturing request-specific state in an application singleton. Console commands have no visitor cookie.
5. Replace the global background ticker with per-visitor advancement based on elapsed time, or a bounded scheduler that iterates active sessions. Define limits on catch-up ticks to prevent excessive CPU/database writes.
6. Scope alarms, event logs, and trends to the same demo identifier, with database migrations and retention cleanup. Do not expose another visitor's logs or alarms.
7. Add per-visitor and per-IP limits, cap active sessions, expire abandoned data, and avoid unbounded cache/database growth.
8. Keep the public control endpoints login-free. Preserve their documented response shape and frontend fetch behavior.

## Acceptance tests before rollout

- Two fresh visitors get different simulation identifiers.
- Starting, stopping, or resetting visitor A's tank does not alter visitor B's state.
- Repeated requests from one visitor retain their own state.
- Invalid or forged identifiers are rejected and replaced.
- API and dashboard use the same visitor context.
- Alarms, events, reports, and trend history cannot leak between visitors.
- The CLI ticker cannot accidentally mutate a visitor's state.
- Expired sessions are cleaned up, and rate limits remain effective.
- The existing public controls work without authentication.

## Rollout

Implement behind a disabled-by-default feature flag, test locally with two separate browser profiles, run automated integration tests, then enable on a staging deployment. Only enable in production after verifying isolation, resource limits and rollback behavior.

**Status:** Design proposal only. Visitor isolation is **not yet implemented** by this document.
