# HallSync

HallSync is a Laravel-based residence and facility management system for shared living spaces such as dormitories, condos, or apartment communities.

It includes:

- Role-based dashboards for `manager`, `resident`, and `handyman`
- Maintenance ticket submission, approval, assignment, and tracking
- Facility booking with slot validation
- Announcement publishing
- Community posting, moderation, comments, and likes
- Private resident concern reporting for management
- Laravel Breeze authentication and profile management

## Stack

- PHP 8.2+
- Laravel 12
- Blade
- Tailwind CSS
- Vite
- SQLite for automated tests, MySQL-compatible schema support for development/production

## Quick Start

1. Install backend dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Create your environment file:

```bash
copy .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`, then run migrations and seed demo users:

```bash
php artisan migrate --seed
```

5. Start the app:

```bash
composer run dev
```

## Seeded Demo Accounts

Running `php artisan migrate --seed` creates these users:

- Manager: `admin@hallsync.com` / `password`
- Handyman: `handyman@hallsync.com` / `password`
- Resident: `test@example.com` / `password`

The seeder uses `updateOrCreate`, so you can re-run it without duplicating those accounts.

## Testing

Run the automated test suite with:

```bash
php artisan test
```

The project is configured to use SQLite in-memory for tests. Recent migration updates were made so tests can run without breaking your normal seeded workflow.

## Project Structure

- `app/Http/Controllers` contains the role-based feature flows
- `app/Models` contains the Eloquent models
- `database/migrations` defines the schema
- `database/seeders` provides demo/test data
- `resources/views` contains the Blade UI for each role
- `tests/Feature` contains feature-level flow coverage

## Professional Hardening Already Applied

This repo now includes:

- Portable ticket-status migration logic that no longer breaks SQLite-based tests
- Guarded duplicate rollback handling for the `assigned_to` ticket migrations
- Default factory role values for predictable test data
- Feature tests for bookings, concerns, community moderation, and database seeding
- Additional operational indexes for common dashboard and listing queries

## Remaining Recommended Improvements

If you want to push this further toward production readiness, the next best steps are:

- Move repeated controller validation into dedicated `FormRequest` classes
- Move authorization logic into Laravel policies
- Add notifications for ticket assignment, concern replies, and moderation results
- Add audit logging for status changes
- Add CI to run formatting and tests on every push

