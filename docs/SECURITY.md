# Security and public demo guidance

This project is an **educational SCADA simulation**, not a real industrial controller. The public demo deliberately permits visitors to start, stop, reset and change modes without signing in. These controls must only affect simulated data, never physical equipment.

## Deployment safeguards

- Use a dedicated demo deployment with no connection to PLCs, plant networks, real sensors or production OT systems.
- Treat simulator state, alarms and logs as public and potentially mutable by anyone. Do not store personal or sensitive operational data.
- Keep `APP_DEBUG=false`, use HTTPS, and configure a unique `APP_KEY` in production.
- Use isolated, least-privilege database and cache credentials. Do not reuse credentials from other applications.
- Use edge-level rate limits, traffic monitoring, and resource limits in addition to the application-level throttles. Rate limiting alone does not prevent intentional disruption.
- Avoid presenting this project as safety-certified or production-ready.
- Review all routes, including web routes, before deployment. Some routes may expose logs, settings or other data.
- If public usage grows, consider isolated per-visitor simulation state so one visitor cannot disrupt another's session.
- Do not publish secrets in issues or logs. Report sensitive security findings privately to the maintainer.

## Public demo design

The `/api/start`, `/api/stop`, `/api/reset`, `/api/manual`, and `/api/auto` endpoints intentionally do not require authentication. This is a usability decision for a simulated environment, not an authorization model suitable for real equipment.

## Automated checks

The CI workflow validates PHP syntax and the frontend build. Additional runtime and integration tests should be introduced before making stronger reliability claims.
