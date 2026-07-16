# HallSync UI/UX Consistency Migration — TODO

## Step 1 — Audit (read-only)
- [ ] Enumerate all Blade files under `resources/views/**` (including `layouts/` and `components/`).
- [ ] Locate inconsistent styling patterns (non-hs classes, inline styles, duplicated Tailwind, typography/padding/radius/shadow mismatches).
- [ ] Identify current shared design-system coverage in `resources/css/app.css`.
- [ ] Produce an “inconsistencies list” grouped by UI primitive: cards/panels, buttons, inputs, tables, badges/chips/status, page headers, modals.

## Step 2 — Refactor (logic unchanged)
- [ ] Update shared components (buttons/inputs/modal/etc.) to enforce hs-* classes.
- [ ] Update shared layout(s)/navigation/header containers for consistent page spacing + hs-page-header.
- [ ] Update all audited pages (auth/manager/resident/handyman/dashboard/forms/tables/notifications/community/maintenance/etc.) to use hs-* wrappers/classes.
- [ ] Remove redundant inline styles only when replaced by equivalent hs-* classes.
- [ ] Preserve responsiveness (desktop + mobile breakpoints).

## Step 3 — Verification
- [x] Run `php artisan test --stop-on-failure` (currently fails due to missing `/login` route exposure in current test environment).
- [ ] Run `npm run build` (or `npm run dev` spot-check if build unavailable).
- [ ] Re-run `php artisan test` after login route issue is resolved.
- [ ] Re-audit modified files to ensure no remaining obvious inconsistencies.

## Step 4 — Report
- [ ] Produce final report listing all modified files and remaining inconsistencies (if any) with exact paths.

