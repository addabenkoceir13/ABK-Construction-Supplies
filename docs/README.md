# ABK Construction Supplies

Internal back-office / admin panel for a construction-supplies business. Tracks materials debt owed by
walk-in clients and by suppliers/tractor-drivers, fuel receipts issued against a small vehicle fleet,
vehicle insurance expiry, and a two-level materials catalog (categories → subcategories). No public/customer
storefront exists — this is an authenticated internal tool only.

App name per `.env.example:1`: `APP_NAME="ABK Construction Supplies"`. Default local DB name is
`dettessofiane` (`.env.example:15`, French *dettes* = "debts") — a naming trace of the app's origin as a
single-purpose debt tracker before the fuel/vehicle/catalog modules were added.

For the as-built architecture (patterns, layer violations, coupling, consistency scoring), see
[`ARCHITECTURE.md`](./ARCHITECTURE.md). For entities and relations, see [`DOMAIN-MODEL.md`](./DOMAIN-MODEL.md).
For per-module detail, see [`MODULES/`](./MODULES/). For house rules actually followed (and not), see
[`CONVENTIONS.md`](./CONVENTIONS.md). For business terms in their original language, see
[`GLOSSARY.md`](./GLOSSARY.md).

---

## Modules

| Module | Business purpose | Docs |
|---|---|---|
| Debt & Billing | Materials sold on credit to walk-in clients and to suppliers/tractor-drivers; payment tracking | [`MODULES/debt-billing.md`](./MODULES/debt-billing.md) |
| Fuel Station | Fuel receipts issued per vehicle, paid/unpaid tracking | [`MODULES/fuel-station.md`](./MODULES/fuel-station.md) |
| Vehicle & Fleet | Vehicle registry, insurance expiry, tractor-driver registry | [`MODULES/vehicle-fleet.md`](./MODULES/vehicle-fleet.md) |
| Catalog | Materials categories/subcategories (line-item pricing source for Debt) | [`MODULES/catalog.md`](./MODULES/catalog.md) |
| Auth & Access | Login/register/logout; no real authorization layer | [`MODULES/auth-access.md`](./MODULES/auth-access.md) |
| Dashboard & Analytics | KPI aggregation across Debt and Fuel Station | [`MODULES/dashboard-analytics.md`](./MODULES/dashboard-analytics.md) |

---

## Stack & versions

Source of truth: `docs/audit/VERSIONS.md` (live audit run against this checkout, not inferred). Numbers below
are copied from there; re-run the audit rather than trusting this table if dependencies change.

### Runtime

| Tool | Version |
|---|---|
| PHP | 8.3.29 (cli, ionCube Loader v15.5.0 loaded) |
| Laravel Framework | 9.52.21 (composer resolves `laravel/framework` to `9.x-dev feb47ba` — a dev-branch alias, not a tagged release; exact tag unconfirmed) |
| Node.js | v22.20.0 |
| npm | 10.9.3 |

Composer constraint is `php: ^8.0.2` (`composer.json:11`) — the installed 8.3.29 satisfies it but the app was
originally targeted at PHP 8.0.

### Key direct dependencies (composer)

| Package | Constraint | Resolved | Notes |
|---|---|---|---|
| laravel/framework | ^9.0 | 9.x-dev | 4 open security advisories per `composer audit` (1 high, 2 medium, 1 unlabeled/CVE-2026-48019) — all against ranges below the currently-resolved dev branch |
| laravel/sanctum | ^2.14 | v2.15.1 | Token guard only; no SPA cookie auth wired (`EnsureFrontendRequestsAreStateful` commented out) |
| laravel/scout | ^10.12 | v10.25.0 | Backs `FuelStation` search — see [`MODULES/fuel-station.md`](./MODULES/fuel-station.md) |
| askedio/laravel-soft-cascade | ^10.0 | 10.0.0 | Cascades soft-deletes; provider registration in `config/app.php` could not be confirmed (see Architecture §7) |
| fruitcake/laravel-cors | ^2.0.5 | v2.2.0 | **Abandoned**, no replacement suggested by maintainer |
| yoeunes/toastr | ^2.3 | v2.3.5 | **Abandoned**; suggested replacement `php-flasher/flasher-toastr-laravel` |

Every direct composer dependency except `php` itself has a major version available. Full outdated/abandoned/
security tables: `docs/audit/VERSIONS.md`.

### Frontend

Laravel Mix 6 (`webpack.mix.js`) compiles Sneat Bootstrap admin-template assets. Bootstrap 5.1, jQuery 3.5,
ApexCharts, DataTables and SweetAlert2 are loaded as static vendor assets under `public/assets/`, not npm
packages. `package.json` pins several packages with `~`/exact versions that block available majors
(`highlight.js`, `jquery`, `axios`, `browser-sync`, `cross-env`, `sass-loader` — see `docs/audit/VERSIONS.md`).

### Search

`config/scout.php:19` defaults to `algolia` if `SCOUT_DRIVER` is unset, but `.env.example:52` sets
`SCOUT_DRIVER=database` — so a fresh checkout following `.env.example` runs Scout against the local database
driver, not a live Algolia index. Whether a deployed `.env` overrides this is unverified (`ARCHITECTURE.md §7`).

---

## How to run (local dev)

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# .env.example defaults: DB_CONNECTION=mysql, DB_DATABASE=dettessofiane, DB_HOST=127.0.0.1
# create the database first, then:
php artisan migrate
php artisan db:seed   # optional — see caution below

npm run dev           # or: npm run watch / npm run hot / npm run prod
php artisan serve
```

**Caution — do not run `db:seed` against anything but a throwaway local database.**
`database/factories/UserFactory.php:22-25` hardcodes a real-looking admin credential pair
(`addasofiane@gmail.com` / `addasofiane2024`) that `DatabaseSeeder::run()` uses to create the seeded admin
user (`docs/audit/raw/07-infrastructure.md`). Treat that credential as already compromised — rotate it before
any shared/staging use.

### Docker (Laravel Sail)

`docker-compose.yml` and `docker/8.1/` are present (Sail is a dev-dependency, `laravel/sail: ^1.0.1`,
`docs/audit/VERSIONS.md`) but the Dockerfile builds PHP 8.1 (`docker-compose.yml:6`) while the platform is
actually running PHP 8.3.29 and `composer.json` targets `^8.0.2` — the Sail image version and the real
runtime have drifted apart. Unverified whether Sail is still used day-to-day.

```bash
./vendor/bin/sail up
```

### Known-broken paths to expect on first run

These are not setup mistakes — they are pre-existing defects documented in `ARCHITECTURE.md`:

- Registration (`/auth/register-basic`) throws a `QueryException` — validates a `username` column that does
  not exist on `users` (`MODULES/auth-access.md`).
- Category create/update/delete redirects to a route name that does not exist
  (`ARCHITECTURE.md` V16) — the operation itself succeeds, the redirect fails.
- `fuel-stations/search` is shadowed by the resource `show` route and never reaches its intended handler
  (`MODULES/fuel-station.md`).
- View paths that differ only by case (`content.debt.*` vs tracked `Debt/`) resolve on Windows/NTFS dev
  boxes but will 404 on a case-sensitive filesystem — see `CONVENTIONS.md`.

---

## Project layout

```
app/
  Console, Exceptions, Http, Models, Providers, Repositories   — the only 6 dirs under app/
  (no Services, Actions, Jobs, Events, Listeners, Policies, Observers, DTO, Http/Requests)
routes/web.php     — 107 LOC, all routes incl. 2 unauthenticated closures
resources/views/   — ~60 Blade templates, Sneat Bootstrap admin theme
database/migrations/ — 15 files, 11 domain+framework tables
docs/audit/        — the raw audit this documentation set is derived from
```

See [`ARCHITECTURE.md`](./ARCHITECTURE.md) for what actually lives where and why the six directories above
are the *complete* list.
