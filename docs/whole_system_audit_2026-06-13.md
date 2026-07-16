# HallSync Whole-System Audit

Audit date: June 13, 2026

## Executive Summary

HallSync is functionally strong for a capstone or controlled pilot, but it is not production-ready.

The principal workflows are implemented and the current automated suite reports 90 passing tests. The production frontend build also succeeds. However, release readiness is blocked by vulnerable PHP dependencies, an inconsistent Composer lockfile, persistent reversible temporary passwords, misleading anonymous-concern behavior, and incomplete production-database verification.

Overall assessment:

- Functional completeness: strong
- Security readiness: needs immediate remediation
- Data integrity: generally thoughtful, with deployment and migration risk
- Backend maintainability: moderate risk
- Frontend maintainability: high risk
- Automated quality: good coverage, but important tests are unnecessarily skipped
- Production operations: incomplete

## Verification Baseline

| Check | Result |
|---|---|
| `php artisan test` | 90 passed, 3 skipped, 326 assertions |
| `npm run build` | Passed |
| `npm audit` | 0 vulnerabilities |
| `composer audit --locked` | Failed: 14 advisories affecting 9 packages |
| `composer validate --strict` | Failed: lockfile is not synchronized |
| `vendor/bin/pint --test` | Failed across controllers, models, migrations, routes, and tests |
| Route registration | Passed, 107 application routes |
| Config/route/view cache compilation | Passed |
| MySQL migration status | Not verified; local MySQL at `127.0.0.1:3306` was unavailable |

## Must Fix

### P0 - Update vulnerable PHP dependencies

The lockfile contains 14 known security advisories. The most serious observed advisory is a high-severity Symfony MIME email/SMTP injection issue. The installed Laravel framework is `12.55.1`, while the reported Laravel advisory affects versions below `12.60.0`.

Affected locked packages include:

- `laravel/framework` `12.55.1`
- `guzzlehttp/psr7` `2.9.0`
- `symfony/http-foundation` `7.4.7`
- `symfony/mime` `7.4.7`
- `symfony/routing` `7.4.6`

Required action:

1. Resolve the Composer manifest mismatch.
2. Update Laravel and Symfony transitive dependencies to patched releases.
3. Re-run `composer audit`, the full test suite, and production cache compilation.

### P0 - Repair the Composer manifest and lockfile

`composer.json` requires `beyondcode/laravel-websockets`, but that package is absent from `composer.lock`. A clean deployment is therefore not reproducible.

HallSync already uses Laravel Reverb, so the BeyondCode websocket package appears redundant and should probably be removed instead of added. Confirm the intended websocket architecture, retain one server implementation, and regenerate the lockfile.

Evidence:

- `composer validate --strict` fails.
- `laravel/reverb`, `pusher/pusher-php-server`, and `beyondcode/laravel-websockets` are simultaneously declared.

### P0 - Stop storing retrievable temporary passwords

Temporary passwords are encrypted reversibly in `users.temporary_password` and displayed from the administrator account page until the user changes the password.

Evidence:

- `app/Models/User.php`: `temporary_password` uses the `encrypted` cast.
- `app/Http/Controllers/Admin/UserController.php`: creation and reset persist the plaintext temporary password.
- `resources/views/admin/user/show.blade.php`: the persisted value is displayed.

Impact:

- Compromise of the database plus `APP_KEY` exposes active temporary credentials.
- Administrators can repeatedly retrieve credentials instead of receiving a one-time disclosure.
- Credential retention is longer than operationally necessary.

Required action:

- Never persist the plaintext or reversibly encrypted temporary password.
- Store only the normal password hash.
- Display the generated value once through flash/session state.
- Require another reset if the administrator loses it.
- Add an expiry timestamp and reject expired temporary credentials.

### P0 - Correct the anonymous concern promise

The concern model supports `is_anonymous`, but administrator views always display the resident's name. The notification method also suppresses updates for anonymous concerns, even though the system retains the owner.

Evidence:

- `resources/views/admin/concerns/index.blade.php` prints `$concern->user->name`.
- `resources/views/admin/concerns/show.blade.php` prints `$concern->user->name`.
- `ConcernController::notifyConcernOwner()` skips anonymous concerns.

The documentation says anonymous reporting retains administrator visibility, but the UI does not explain that distinction. This creates a trust and privacy problem.

Required action:

- Define the exact anonymity policy.
- If anonymous means hidden from assigned staff or general managers, enforce that at query/view/policy level.
- If administrators always see identity, say so clearly before submission.
- Continue sending owner notifications without exposing identity to other participants.
- Add feature tests for anonymous list, detail, assignment, exports, logs, and notifications.

### P1 - Restore real dashboard test coverage

Three dashboard tests are skipped whenever the test database is not MySQL because they claim `DAYOFWEEK()` is still used. The implementation now groups day-of-week values in PHP, so the skip condition is stale.

Impact:

- Manager dashboard regressions can pass CI unnoticed.
- The test report appears greener than the actual coverage.
- PHPUnit 12 compatibility warnings are also present because test groups use doc comments.

Required action:

- Remove the MySQL-only skips.
- Convert PHPUnit metadata doc comments to attributes.
- Run these tests in SQLite and MySQL CI jobs.

### P1 - Verify the complete migration chain on MySQL

SQLite tests pass, but the configured MySQL service was unavailable during this audit. The migration history includes duplicate role and assignment migrations, multiple enum changes, constraint replacement, and compatibility guards.

Required action:

- Run `migrate:fresh --seed` against the supported MySQL version.
- Test upgrading a copy of an existing database, not only a fresh database.
- Verify every `down()` path used by the deployment process.
- Produce and review a schema dump after stabilization.

### P1 - Add production security headers and deployment defaults

Only private media responses set a Content Security Policy. Normal pages have no application-level CSP, clickjacking protection, referrer policy, permissions policy, or HSTS configuration.

The example environment also uses development-oriented defaults such as `APP_DEBUG=true`, predictable Reverb credentials, HTTP URLs, and unencrypted file sessions.

Required action:

- Add environment-aware security headers.
- Set production documentation/defaults for `APP_DEBUG=false`, HTTPS cookies, trusted proxies, secure Reverb credentials, and production mail.
- Decide whether session encryption is required for the deployment threat model.
- Add a deployment checklist that fails closed when critical secrets are placeholders.

### P1 - Paginate operational lists

The manager ticket list loads up to 500 records into memory and the resident ticket and concern lists load all matching rows. Navigation notifications also fetch complete result sets before filtering and taking three entries.

Impact:

- Slow pages and high memory use as records grow.
- Heavy repeated queries from shared layout composers.
- Older tickets silently disappear after the manager's 500-record limit.

Required action:

- Use server-side pagination and validated filters.
- Count unread notifications in SQL and fetch only the preview rows.
- Add indexes based on the final filter and sort patterns.

### P1 - Reduce realtime polling load

Every live panel polls `/live/heartbeat` every five seconds. A manager heartbeat performs seven latest-timestamp queries; a resident heartbeat performs five.

At modest concurrency this creates a sustained database query load even when nothing changes.

Required action:

- Prefer authenticated websocket events.
- If polling remains as fallback, increase the interval, pause in hidden tabs, use backoff, and cache a single role-scoped revision token.
- Instrument request rate and database time for this endpoint.

## Improvements

### Architecture and backend

1. Split `DashboardController`, `BookingController`, and `TicketController` into smaller application services or action classes.
2. Move repeated inline validation into dedicated Form Requests.
3. Use policies consistently instead of mixing policies, role checks, and `abort_unless`.
4. Queue email notifications. The current notifications are synchronous and do not implement `ShouldQueue`.
5. Replace generated IDs based on `uniqid()` with ULIDs, UUIDs, or a database-safe reference generator.
6. Complete or remove the resident roster workflow. It is seeded and modeled, but account provisioning does not claim roster entries and move-out explicitly skips roster updates.
7. Centralize lifecycle transitions for tickets, bookings, community posts, and concerns to prevent controller-specific state rules from drifting.

### Database and analytics

1. Remove N+1 analytics queries in resident hotspot and staff performance loops.
2. Avoid loading complete ticket collections merely to calculate counts and age buckets.
3. Clarify analytics definitions. Some metrics compare records created in a period with records resolved in that period, which can produce misleading resolution rates.
4. Add database-level constraints where practical for lifecycle values and timestamp ordering.
5. Add retention rules for audit logs, evidence, deleted records, notifications, and exported data.
6. Add backup, restore, and disaster-recovery tests.

### Frontend and UI/UX

1. Break up `resources/views/layouts/app.blade.php`, which is roughly 7,700 lines.
2. Extract page CSS and JavaScript from Blade files. At least 53 views contain style blocks and 30 contain script blocks.
3. Create reusable design tokens and components for forms, buttons, status badges, cards, modals, tables, empty states, and responsive behavior.
4. Replace inline `onclick` handlers with module event listeners.
5. Bundle Chart.js locally through Vite instead of loading it directly from a CDN on operational dashboards.
6. Remove duplicate Google Font imports and define one typography strategy.
7. Correct source encoding corruption such as `â€“`, `â€”`, and `â€”`-style artifacts.
8. Add automated accessibility checks for labels, keyboard operation, focus trapping, reduced motion, color contrast, and modal announcements.
9. Run browser tests at mobile, tablet, laptop, and wide desktop sizes.
10. Add loading, error, empty, and offline states for every asynchronous interaction.

### Security and privacy

1. Add explicit authorization tests for every route and role combination.
2. Add malware scanning or quarantine for uploaded evidence and videos.
3. Sanitize attachment download names and define allowed MIME/content-disposition behavior.
4. Add audit events for evidence deletion, exports, profile photo changes, and sensitive account views.
5. Review whether every authenticated user should be able to retrieve every user's profile photo.
6. Add administrator re-authentication for password resets, account deletion, move-out, and bulk deactivation.
7. Document privacy handling for concern evidence, user activity metadata, IP addresses, and user agents.

### DevOps and quality assurance

1. Add CI jobs for Composer validation, Composer audit, Pint, tests, frontend build, and npm audit.
2. Test both SQLite and MySQL; SQLite alone does not validate production SQL and migrations.
3. Add Laravel Dusk or Playwright tests for the resident, manager, and staff critical journeys.
4. Add static analysis with Larastan/PHPStan and frontend linting.
5. Add production health checks for database, queue worker, Reverb, mail, storage, and scheduler.
6. Add structured logging, exception monitoring, slow-query monitoring, and alerting.
7. Define deployment, rollback, backup, and secret-rotation procedures.

## Recommended Implementation Order

### Release blockers

1. Fix Composer manifest/lock consistency.
2. Upgrade all vulnerable PHP dependencies.
3. Remove persistent temporary-password recovery.
4. Correct and test anonymous concern behavior.
5. Run full MySQL fresh-install and upgrade migration tests.

### Reliability

1. Re-enable dashboard tests.
2. Add CI and formatting/static-analysis gates.
3. Paginate lists and optimize notification queries.
4. Reduce heartbeat polling load.
5. Queue notifications and add worker monitoring.

### Product quality

1. Split the shared layout and page-level assets.
2. Standardize reusable UI components.
3. Fix encoding and accessibility issues.
4. Add browser-based end-to-end and responsive testing.
5. Clarify analytics definitions and operational KPIs.

## Release Decision

Current recommendation: **No-go for public production deployment.**

Recommended use today: supervised capstone demonstration or limited internal testing with non-sensitive data.

The system can move toward a production candidate after the P0 items are fixed, the MySQL migration chain is verified, the PHP advisory scan is clean, and the skipped dashboard tests run successfully in CI.
