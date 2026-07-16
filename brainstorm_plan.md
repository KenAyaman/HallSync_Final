# HallSync UI/UX Consistency Migration — Brainstorm Plan (Pre-Refactor)

## Scope / constraints
- Migrate Blade views + shared components to use the repo’s shared HallSync design-system primitives:
  - `hs-card`, `hs-panel`
  - `hs-btn`, `hs-btn-primary`, `hs-btn-secondary`, `hs-btn-danger`
  - `hs-input`
  - `hs-page-header`
  - `hs-table`
  - `hs-modal-dialog`
  - `hs-status`, `hs-badge`, `hs-chip`
- Do **not** change business logic, routes, controllers, policies, permissions, or data.
- Keep palette and theme decisions intact (no redesign, no color drift).
- Preserve responsive behavior (desktop + mobile).

## Information gathered (from repo audit)
1) **New design system exists** in `resources/css/app.css` with the required `hs-*` classes.
2) Shared components already map many primitives to `hs-*`:
   - `resources/views/components/primary-button.blade.php` → `hs-btn hs-btn-primary`
   - `resources/views/components/secondary-button.blade.php` → `hs-btn hs-btn-secondary`
   - `resources/views/components/danger-button.blade.php` → `hs-btn hs-btn-danger`
   - `resources/views/components/text-input.blade.php` → `hs-input`
   - `resources/views/components/modal.blade.php` uses `hs-modal-dialog`
3) Layouts/nav currently rely on legacy role/module classes and/or inline CSS blocks:
   - `resources/views/layouts/navigation.blade.php`: `role-*`
   - `resources/views/layouts/admin-nav.blade.php`: `admin-*` + inline `<style>`
   - `resources/views/layouts/handyman-nav.blade.php`: `staff-*` + inline `<style>`
   - `resources/views/layouts/guest.blade.php`: guest auth + inline `<style>`
4) Ticket pages show mixed usage:
   - `resident/tickets/index.blade.php`: `resident-*` + inline `style="display: none;"`
   - `resident/tickets/show.blade.php`: outer uses `hs-ticket-page/hs-ticket-hero/hs-ticket-panel` but inner still uses `resident-ticket-*` classes.
   - `resident/tickets/track.blade.php`: `ticket-track-*` classes.

## Migration approach (safe + deterministic)
### Step ordering (to reduce UI regressions)
1) **Components & primitives**
   - Verify that all shared primitives exist in `app.css` and adjust only *markup/class composition* in components if needed.
   - Avoid deleting legacy CSS yet; migrate markup first.
2) **Layouts / shared wrappers**
   - Introduce `hs-page-header` and/or `hs-card/hs-panel` wrappers in layouts where feasible.
   - Keep existing role/module navigation structure and classnames unless there is an `hs-*` direct replacement that preserves behavior.
3) **Page-by-page migration**
   - For each module, replace legacy wrapper classes with `hs-*` wrappers:
     - card/panel containers
     - page headers
     - buttons/inputs
     - tables
     - badges/chips/status
     - modals

### Legacy → `hs-*` mapping rules
Use the following *target mapping* when editing Blade markup:

- **Cards / panels**
  - Replace outer containers like `resident-page-panel`, `resident-card`, `admin-panel-card`, etc. with `hs-card` or `hs-panel` depending on semantic intent:
    - `hs-card`: repeatable list items / tiles / content blocks
    - `hs-panel`: larger section containers
  - Preserve existing `data-*` hooks and aria attributes.

- **Page headers**
  - Replace heading blocks that have repeated kicker + title + subtitle patterns with `hs-page-header`.
  - Do not change H1/H2 hierarchy.

- **Buttons**
  - Replace `<button>/<a>` with classes derived from:
    - `resident-page-btn-*`, `resident-ticket-btn-*`, `ticket-track-btn-*`, `admin-*btn*`, etc.
  - Target classes:
    - Primary/gradient actions → `hs-btn hs-btn-primary`
    - Secondary/tertiary actions → `hs-btn hs-btn-secondary`
    - Destructive actions → `hs-btn hs-btn-danger`
  - If an action is currently a ghost style and `hs` system lacks an exact ghost variant, keep legacy ghost *until* `hs-btn-secondary` is confirmed visually equivalent (no palette drift).

- **Inputs / selects / textareas**
  - For any input/select/textarea component usage, prefer `x-resident`/`x-text-input`/`hs-input` class.
  - Replace manual inline sizing/radius with `hs-input` and let `app.css` govern sizing.

- **Tables**
  - For any table markup, replace legacy `table` classes with `hs-table`.
  - Preserve scroll wrappers (`overflow-x-auto`, etc.) and responsive behavior.

- **Badges / chips / status**
  - Replace `resident-badge-*`, `resident-status-chip-*`, etc. with `hs-badge` / `hs-chip` / `hs-status`.
  - For status tones, only switch to `hs-status` if there is an existing mapping function/var that produces correct tone classes (e.g. `hs-badge hs-badge-danger`).

- **Modals**
  - If the repo uses `resources/views/components/modal.blade.php`, ensure the modal container consistently uses `hs-modal-dialog`.
  - Remove duplicated radius/shadow styles only if the `hs-*` class provides the same effect.

### Handling inline styles
- Remove inline styles **only** when the equivalent behavior exists in `hs-*` or in existing global responsive rules.
- Examples discovered:
  - `style="display: none;"` used for progressive reveal sections in tickets.
  - If no `data-*`/CSS alternative exists, keep inline toggling but consider swapping to a class (e.g. `hidden`) only if `app.css` uses the same semantics.

## Target files (first refactor wave)
Because this is a large project-wide change, first wave focuses on the highest-leverage shared surfaces:

### Shared layout + navigation
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/layouts/admin-nav.blade.php`
- `resources/views/layouts/handyman-nav.blade.php`
- `resources/views/layouts/guest.blade.php`

### Shared components
- `resources/views/components/modal.blade.php`
- `resources/views/components/text-input.blade.php`
- `resources/views/components/primary-button.blade.php`
- `resources/views/components/secondary-button.blade.php`
- `resources/views/components/danger-button.blade.php`

### Pages in first visible module example (to validate approach)
- `resources/views/resident/tickets/index.blade.php`
- `resources/views/resident/tickets/show.blade.php`
- `resources/views/resident/tickets/track.blade.php`

After validating that screenshots (or a local dev run) show no regressions, repeat for remaining modules:
- auth, profile, dashboard (resident/manager/handyman), admin pages (announcements/bookings/community/concerns/tickets/users), notifications, resident concerns/bookings/community/announcements.

## Acceptance criteria for “done” (what we will verify)
- Every Blade view has been audited.
- All replaced styling uses `hs-*` primitives (no duplicated inline radius/shadow where `hs-*` exists).
- Mobile and desktop layouts remain functional and responsive.
- No functionality changes (JS hooks, form behavior, aria, hidden sections remain equivalent).


