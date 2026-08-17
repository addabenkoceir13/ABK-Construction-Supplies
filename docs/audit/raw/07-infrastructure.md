# Scope: Infrastructure/Bootstrap

## Files

**app/Providers/** (335 LOC total across this + Console + Exceptions)
| File | LOC |
|---|---|
| app/Providers/AppServiceProvider.php | 29 |
| app/Providers/AuthServiceProvider.php | 30 |
| app/Providers/BroadcastServiceProvider.php | 21 |
| app/Providers/MenuServiceProvider.php | 32 |
| app/Providers/EloquentRepositoryProvider.php | 54 |
| app/Providers/RouteServiceProvider.php | 54 |
| app/Providers/EventServiceProvider.php | 42 |

**app/Console/**
| File | LOC |
|---|---|
| app/Console/Kernel.php | 32 |

`app/Console/Commands/` does not exist on disk (Kernel.php:28 loads it via `$this->load(__DIR__.'/Commands')`, which is a no-op directory scan — confirmed absent via `ls`).

**app/Exceptions/**
| File | LOC |
|---|---|
| app/Exceptions/Handler.php | 41 |

**routes/**
| File | LOC |
|---|---|
| routes/web.php | 107 |
| routes/api.php | 19 |
| routes/channels.php | 18 |
| routes/console.php | 19 |

**config/** (1923 LOC total, 20 files) — all files present are unmodified-looking Laravel 9 stubs except `constant.php`, `variables.php`, `flasher.php`, `flasher_toastr.php`. Full list: app.php, auth.php, broadcasting.php, cache.php, constant.php, cors.php, database.php, filesystems.php, flasher.php, flasher_toastr.php, hashing.php, logging.php, mail.php, queue.php, sanctum.php, scout.php, services.php, session.php, variables.php, view.php.

**database/migrations/** (15 files, chronological)
| File |
|---|
| 2014_10_12_000000_create_users_table.php |
| 2014_10_12_100000_create_password_resets_table.php |
| 2019_08_19_000000_create_failed_jobs_table.php |
| 2019_12_14_000001_create_personal_access_tokens_table.php |
| 2024_09_19_094804_create_tractor_drivers_table.php |
| 2024_09_19_164030_create_categories_table.php |
| 2024_09_19_164035_create_subcategories_table.php |
| 2024_09_19_164130_create_debts_table.php |
| 2024_09_19_164340_create_debt_products_table.php |
| 2024_10_24_192726_add_debt_paid_to_debts_table.php |
| 2024_11_03_090234_create_vehicles_table.php |
| 2024_11_03_093212_create_insurance_vehicles_table.php |
| 2024_11_03_094248_create_fuel_stations_table.php |
| 2024_11_17_185352_add_status_to_fuel_stations_table.php |
| 2025_08_18_144113_create_debt_histories_table.php |

**database/seeders/**
| File |
|---|
| database/seeders/DatabaseSeeder.php |
| database/seeders/CategorySeeder.php |
| database/seeders/SubCategory.php (class `SubCategory`, filename does not follow `*Seeder.php` convention used by its siblings) |
| database/seeders/SupplierSeeder.php (seeds `tractor_drivers`, not a "supplier" table — name/domain mismatch) |

**database/factories/**
| File |
|---|
| database/factories/UserFactory.php |

## Classes & Responsibilities

### app/Providers/AppServiceProvider.php:8
- `namespace App\Providers`, `extends Illuminate\Support\ServiceProvider`
- `register()` (AppServiceProvider.php:15) — empty.
- `boot()` (AppServiceProvider.php:25) — calls `Paginator::useBootstrapFive()` (AppServiceProvider.php:27) to render Bootstrap-5-styled pagination links app-wide.
- No constructor dependencies.

### app/Providers/AuthServiceProvider.php:8
- `extends Illuminate\Foundation\Support\Providers\AuthServiceProvider`
- `$policies` array (AuthServiceProvider.php:15) is empty/commented-out — no Eloquent Policy classes are registered.
- `boot()` (AuthServiceProvider.php:24) calls `registerPolicies()` only.
- Confirmed via directory check: `app/Policies` does not exist anywhere in the project — all authorization logic (if any) must live inline in controllers, which are out of this scope.

### app/Providers/BroadcastServiceProvider.php:8
- `extends Illuminate\Support\ServiceProvider`
- `boot()` (BroadcastServiceProvider.php:15) calls `Broadcast::routes()` and requires `routes/channels.php`.
- **Not registered** in `config/app.php:176` — the line `// App\Providers\BroadcastServiceProvider::class,` is commented out. This means `Broadcast::routes()` never runs and `routes/channels.php` is never loaded in the running app, making the entire broadcasting layer (config/broadcasting.php, routes/channels.php) dead/unreachable code.

### app/Providers/MenuServiceProvider.php:7
- `extends Illuminate\Support\ServiceProvider`
- `boot()` (MenuServiceProvider.php:24) reads `resources/menu/verticalMenu.json` via `file_get_contents` + `json_decode` (MenuServiceProvider.php:26-27) and shares it to all views as `menuData` via `\View::share` (MenuServiceProvider.php:30). No error handling if the JSON file is missing or malformed — `file_get_contents` would emit a warning and `json_decode(false)` return null, silently breaking the admin menu on every page.

### app/Providers/EloquentRepositoryProvider.php:25
- `extends Illuminate\Support\ServiceProvider`
- `register()` (EloquentRepositoryProvider.php:32-43) binds 9 repository interfaces to Eloquent implementations: Category, SubCategory, Debt, DebtHistory, DebtProduct, TractorDriver, Vehicle, InsuranceVehicle, FuelStation (EloquentRepositoryProvider.php:34-42). This is the app's repository-pattern wiring — confirms a Repository layer exists (`App\Repositories\*`), out of this scope's file list but relevant as the DI boundary this scope owns.
- Note: `Supplier` domain (seen in routes as `debt-supplier`, `SupplierController`) has no repository binding here — it appears to reuse the `TractorDriver` repository/model under the hood (per SupplierSeeder.php:5 which imports `App\Models\TractorDriver`), i.e. "Supplier" is a UI alias over the `tractor_drivers` table, not a distinct bound repository.

### app/Providers/RouteServiceProvider.php:11
- `extends Illuminate\Foundation\Support\Providers\RouteServiceProvider`
- `public const HOME = '/home'` (RouteServiceProvider.php:20) — declared but `/home` is not defined in routes/web.php (dashboard root route is `/`, name `dashboard-analytics`). Dead/stale constant likely left from the Laravel breeze/UI scaffold.
- `boot()` (RouteServiceProvider.php:27) registers rate limiting then loads `routes/api.php` under `api` middleware+prefix and `routes/web.php` under `web` middleware (RouteServiceProvider.php:31-40).
- `configureRateLimiting()` (RouteServiceProvider.php:48) — API limiter: 60 req/min per user id or IP (RouteServiceProvider.php:50-52). No custom limiter for `web` routes.

### app/Providers/EventServiceProvider.php:10
- `extends Illuminate\Foundation\Support\Providers\EventServiceProvider`
- `$listen` (EventServiceProvider.php:17-21): only the framework default `Registered => [SendEmailVerificationNotification]`.
- `shouldDiscoverEvents()` returns `false` (EventServiceProvider.php:38-40) — auto event discovery disabled, and no custom events/listeners are registered manually either. Combined with the confirmed absence of `app/Listeners` and `app/Jobs` directories, this application has **no event-driven side effects** anywhere — all business logic (debt payment, status changes, etc.) must run synchronously inline in controllers.

### app/Console/Kernel.php:8
- `extends Illuminate\Foundation\Console\Kernel`
- `schedule()` (Kernel.php:16-19) — empty, commented-out example only. No scheduled tasks run (no cron-driven maintenance, no report generation, no cleanup jobs).
- `commands()` (Kernel.php:26-31) loads `app/Console/Commands` (non-existent, no-op) and requires `routes/console.php`.

### app/Exceptions/Handler.php:8
- `extends Illuminate\Foundation\Exceptions\Handler`
- `$dontReport` empty (Handler.php:15-17).
- `$dontFlash` (Handler.php:24-28): standard password fields.
- `register()` (Handler.php:35-40) registers an empty `reportable()` callback — no custom exception reporting/logging/notification (e.g., no Slack/Sentry hook) is wired up. All errors fall through to Laravel's default logging channel only.

## Data Flow (entrypoint -> exit)

1. HTTP request -> `bootstrap/app.php` (not in scope) -> `RouteServiceProvider::boot()` (RouteServiceProvider.php:27) dispatches to `routes/web.php` (web middleware) or `routes/api.php` (api middleware, `auth:sanctum` for `/user`).
2. `routes/web.php:33-44` — unauthenticated-looking theme/lang switch closures are wrapped in `middleware(['auth'])`, so they actually require login (fine).
3. `routes/web.php:47-48` — root `/` and `/template` map directly to `App\Http\Controllers\dashboard\Analytics@index2` / `@index` using legacy **string-based controller action syntax** (`'App\Http\Controllers\...@method'`) rather than `[Controller::class, 'method']` array syntax used elsewhere in the same file (e.g. web.php:57). Inconsistent convention across the same file.
4. `routes/web.php:51-82` — main authenticated resource routes (`services/building-materals` [sic, typo for "materials"], `services/subcategory`, `services/tractor-driver`, `services/vehicle`, `debt`, `debt-supplier`, `fuel-stations`, `print/printer-facteur/...`) all wrapped in `middleware(['auth'])`, dispatching into controllers under `App\Http\Controllers\*` (out of this scope).
5. `routes/web.php:85-90` — auth routes (login/register/forgot-password/logout) also use string-based `Controller@method` syntax, no CSRF/throttle middleware visible at the route-definition level beyond Laravel's default `web` group.
6. `routes/web.php:92-99` — **`GET list/debt/supplier/`, no middleware at all**, sits outside every `Route::group(['middleware' => ['auth']])` block in the file. The closure directly queries `App\Models\Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->get()` (web.php:96) and renders `content.Liste.index` — this exposes unpaid customer debts (names, phone numbers via `fullname`/`phone` columns, amounts) to **any unauthenticated visitor**. Also a direct `Model::` call inside a route closure rather than a controller/repository — bypasses the `EloquentRepositoryProvider` binding pattern used elsewhere.
7. `routes/web.php:101-105` — **`GET password/hash`, no middleware**, hardcodes the string `'123456789'`, runs `Hash::make()` on it, and echoes the resulting bcrypt hash back to any anonymous caller. This looks like a leftover developer utility route for manually minting a password hash to paste into the database — publicly reachable in what config/app.php:31 defaults to a `production` environment.
8. Broadcasting path (`BroadcastServiceProvider` -> `routes/channels.php`) is provider-disabled (see above) — channel `App.Models.User.{id}` (channels.php:16) is unreachable dead code.
9. Console entrypoint: `artisan` -> `Console\Kernel::commands()` (Kernel.php:26) -> `routes/console.php:17` registers only the stock `inspire` Artisan command. No custom scheduled or ad-hoc commands exist in the codebase.
10. Uncaught exceptions anywhere in the app funnel through `App\Exceptions\Handler::register()` (Handler.php:35), which does nothing beyond Laravel defaults (no external alerting).

## External Dependencies (packages, APIs, queues)

From `composer.json` (require section), as they relate to this scope's config/providers:
- `laravel/framework: ^9.0`, `php: ^8.0.2`
- `laravel/sanctum` — config/sanctum.php present, wired via `routes/api.php:17` (`auth:sanctum` on `/user`). Only one API route exists total.
- `laravel/scout: ^10.12` — config/scout.php fully configured (Algolia driver by default per scout.php:19, `env('SCOUT_DRIVER','algolia')`) but no `ALGOLIA_*` usage or searchable model found in this scope; cannot confirm from this scope alone whether Scout is actually used anywhere (see Open Questions).
- `fruitcake/laravel-cors` — config/cors.php: `allowed_origins => ['*']` (cors.php:22) with `supports_credentials => false` (cors.php:32), scoped to `api/*` and `sanctum/csrf-cookie` paths (cors.php:18). Wide-open CORS but low risk given no credentials and a near-empty API surface today.
- `askedio/laravel-soft-cascade: ^10.0` — relevant because every migration in this scope pairs `softDeletes()` with `->constrained(...)->cascadeOnDelete()` (e.g. debts_table.php:18-19, debt_products_table.php:18-19, insurance_vehicles_table.php:18, fuel_stations_table.php:18, debt_histories_table.php:18). Native DB `cascadeOnDelete` is a real FK constraint that fires on hard deletes only; since all these models soft-delete, the package is presumably what propagates soft-deletes to children — but no config for it was found in `config/` and its service provider registration wasn't visible in `config/app.php`'s providers array (app.php:139-183 lists no `AskedIo`/`SoftCascade` provider), so cascade-soft-delete behavior could not be confirmed as active from files in this scope.
- `yoeunes/toastr` (PHPFlasher) — config/flasher.php, config/flasher_toastr.php: loads flasher JS/CSS from a **jsdelivr CDN by default** (flasher.php:53, flasher_toastr.php:11-12) rather than local vendor assets (`use_cdn => true`, flasher.php:92) — external runtime dependency on a third-party CDN for a production admin panel.
- Mail: `config/mail.php` defaults to `smtp` mailer via `smtp.mailgun.org` (mail.php:39) — Mailgun-oriented but driven entirely by env vars, no secrets in-repo.
- Queue: `config/queue.php:16` defaults to `sync` (`env('QUEUE_CONNECTION','sync')`) — no real queue backend configured; combined with the empty EventServiceProvider/absent Jobs directory, nothing in this app currently runs asynchronously.
- Broadcasting: `config/broadcasting.php:18` defaults to `null` driver, and the provider that would activate it is commented out (see above) — effectively fully inert.

## Smells & Debt

| file | line | issue | severity |
|---|---|---|---|
| routes/web.php | 101-105 | Public, unauthenticated `GET password/hash` route hardcodes password `'123456789'`, hashes it with bcrypt and returns it to any anonymous visitor — looks like a leftover debug/dev-ops utility route shipped in an app whose `config/app.php:31` env defaults to `production`. | 5 |
| routes/web.php | 92-99 | Public, unauthenticated `GET list/debt/supplier/` route queries and renders all unpaid debts (customer name, phone, amounts) with no `auth` middleware — sits outside every middleware group in the file (compare to the enclosing group at web.php:51-82). Also calls `App\Models\Debt::` directly from a route closure instead of going through a controller/repository. | 5 |
| database/factories/UserFactory.php | 22-25 | Hardcoded real-looking admin credentials committed to source control: `email => 'addasofiane@gmail.com'`, `password => Hash::make('addasofiane2024')`. This factory is invoked by `DatabaseSeeder::run()` (DatabaseSeeder.php:17) to create the actual seeded admin user, so these credentials are a live/plausible login for the app if ever seeded against a real deploy. | 5 |
| database/migrations/2024_11_17_185352_add_status_to_fuel_stations_table.php | 18 | `->after(' amount')` — leading space typo in the column name argument. `fuel_stations` table's real column is `amount` (no leading space), so this `after()` positional hint references a non-existent column name; MySQL/MariaDB would likely still add the column (silently ignoring/erroring on the position hint depending on driver strictness) but the intended column ordering is broken. Could not execute migrations to confirm actual DB behavior (read-only). | 2 |
| config/constant.php | 15-17 | `TRACTORDRIVER_TYPE.DRIVERY => 'drivery'` (typo, missing "e") does not match the enum values defined in `database/migrations/2024_09_19_094804_create_tractor_drivers_table.php:20` (`enum('type', ['delivery', 'normal'])`). If this constant were ever used to write to the `type` column it would violate the enum constraint and fail. Confirmed via repo-wide grep that `DRIVERY`/`drivery` is referenced nowhere else in `app/` — currently dead/unused, but a landmine for whoever wires it up. | 3 |
| app/Providers/BroadcastServiceProvider.php | 8 | Provider is fully implemented but never registered — `config/app.php:176` has it commented out (`// App\Providers\BroadcastServiceProvider::class,`). `Broadcast::routes()` never executes and `routes/channels.php` (18 LOC, defines the `App.Models.User.{id}` channel) is dead code. | 2 |
| app/Providers/MenuServiceProvider.php | 26-27 | `file_get_contents(base_path('resources/menu/verticalMenu.json'))` has no existence/error check before `json_decode`; a missing/invalid JSON file degrades silently (PHP warning + `menuData` becomes `[null]`) rather than failing loudly, and would break admin-panel navigation app-wide with no clear error trail (Exceptions\Handler.php:35-40 has no reportable hook to catch it either). | 2 |
| database/migrations/2024_09_19_164340_create_debt_products_table.php | 24 | `$table->enum('status', [1,0])` — integer literals used as enum values (stored as strings `'1'`/`'0'` by MySQL); unconventional and easy to confuse with a boolean/tinyint column. No corresponding named constant in `config/constant.php` (unlike `DEBTS_STATUS`/`TRACTORDRIVER_STATUS`), so callers must use magic `1`/`0` literals. | 2 |
| database/migrations/2024_09_19_164340_create_debt_products_table.php | 21 | `$table->string('quantity')` — quantity stored as a string rather than integer/decimal; later arithmetic on this column (e.g., totals) requires implicit/explicit casting. | 2 |
| database/seeders/SubCategory.php | 9 | Seeder class named `SubCategory` (matches an actual Eloquent model name `App\Models\SubCategory`, forcing the import alias `ModelsSubCategory` at SubCategory.php:5) instead of the `*Seeder` convention its siblings (`CategorySeeder`, `SupplierSeeder`) follow. Confusing naming, easy to import the wrong class elsewhere. | 1 |
| database/seeders/SupplierSeeder.php | 9-29 | Class is named `SupplierSeeder` and its `run()` creates `App\Models\TractorDriver` rows (SupplierSeeder.php:18,24) — no `Supplier` model/table exists; "Supplier" is purely a UI-level alias over `tractor_drivers`. Naming mismatch between seeder intent and actual schema/domain. | 1 |
| app/Providers/RouteServiceProvider.php | 20 | `const HOME = '/home'` does not correspond to any route defined in `routes/web.php` (root is `/`, named `dashboard-analytics`). Stale scaffold constant. | 1 |
| app/Providers/EloquentRepositoryProvider.php | 25-43 | `register()` binds 9 repositories manually with no grouping/array-driven approach — fine at this size, but will not scale cleanly; also asymmetric with the "Supplier" domain, which has no repository binding of its own (see Classes section). | 1 |
| database/migrations/2025_08_18_144113_create_debt_histories_table.php | 19 | `decimal('amount', 8, 2)` uses a smaller precision than the equivalent monetary columns elsewhere (`debts.total_debt_amount`, `debts.rest_debt_amount`, `debt_products.amount`, `fuel_stations.amount` all use `decimal(20,2)`). Inconsistent precision across money columns in the same schema; max value here is capped at 999,999.99. | 2 |
| database/migrations/2024_09_19_164130_create_debts_table.php | 23-24 | `total_debt_amount` and `rest_debt_amount` are both `nullable()` decimals with no default. Git history shows a prior bug fix "fix bug calcule rest debt amount" (commit `2da35ec`) — nullable money columns with no default are a recurring source of null-arithmetic bugs; worth a non-null default of `0` at the schema level rather than relying on application code everywhere. | 2 |
| routes/web.php | 47-48, 85-90 | Legacy string-based `'Controller@method'` route actions used alongside array/`::class`-based syntax in the same file (e.g. web.php:53-79). Inconsistent convention; string actions also don't get IDE/static-analysis reference checking. | 1 |
| routes/web.php | 53 | `services/building-materals` — typo for "materials" baked into a public route path/resource name (`.names('services.building-materials')` at web.php:53 is spelled correctly, but the URI segment itself is misspelled), so the URI and the route name disagree in spelling. | 1 |
| config/scout.php | 19 | `env('SCOUT_DRIVER', 'algolia')` — full Scout/Algolia config present and defaulting to a live external search engine, but no evidence in this scope of it being used (no scheduled sync command, no queue-based indexing observed). Could not confirm usage without inspecting Models (out of scope). | — (see Open Questions) |

## Open Questions

- Is `laravel/scout` (config/scout.php) actually wired to any Eloquent model (`Searchable` trait)? Nothing in this scope (Providers/Console/routes/migrations) references it, and no `ALGOLIA_APP_ID`/`ALGOLIA_SECRET` usage was found in scope. Would need to check `app/Models/*` to confirm.
- Is `askedio/laravel-soft-cascade` actually registered/active? Its service provider was not found in `config/app.php`'s `providers` array (app.php:139-183), yet every migration in this scope pairs `softDeletes()` with `cascadeOnDelete()` FKs, which only cooperate correctly with soft-deletes if that package (or equivalent custom logic, e.g. model `deleting` events) is active. Confirming this requires inspecting `app/Models/*` (out of scope) for `SoftCascadeTrait` usage or boot-time event registration.
- Does `.env` (not committed, could not be read/should not be read) actually set `APP_ENV=production` and `APP_DEBUG=false` on the real deployment? `config/app.php:31,44` default to `production`/`false` if unset, but the two dangerous public routes in `routes/web.php:92-105` are dangerous regardless of env.
- Whether the `GET password/hash` and `GET list/debt/supplier/` routes (web.php:92-105) are still actively used/needed by anyone, or are safe to delete outright — could not determine intent from static analysis alone; flagged for the team to confirm before removal.
- Whether `database/migrations/2024_11_17_185352_add_status_to_fuel_stations_table.php:18`'s `->after(' amount')` typo actually causes a migration failure or is silently tolerated — could not run migrations in this read-only pass.
- `app/Http/Controllers/*`, `app/Models/*`, `app/Repositories/*`, `app/Http/Middleware/*` are referenced extensively from this scope's routes/providers but are explicitly out of scope for this pass; a full picture of "fat controller" / N+1 / raw-DB smells in the request path (e.g., the many `DB::beginTransaction()/commit()/rollBack()` pairs observed in `DebtController`, `DebtWithSupplierController`, `FulstationController`, `SupplierController`, `TractorDriverController` via a repo-wide grep) should be covered by whichever scope owns `app/Http/Controllers`.

docs/audit/raw/07-infrastructure.md
