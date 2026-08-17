# Scope: Auth & Access

## Files

| File | LOC |
|---|---|
| app/Http/Controllers/authentications/ForgotPasswordBasic.php | 14 |
| app/Http/Controllers/authentications/LoginBasic.php | 32 |
| app/Http/Controllers/authentications/LogoutBasic.php | 19 |
| app/Http/Controllers/authentications/RegisterBasic.php | 40 |
| app/Models/User.php | 46 |
| app/Http/Middleware/Authenticate.php | 21 |
| app/Http/Middleware/EncryptCookies.php | 17 |
| app/Http/Middleware/LocaleMiddleware.php | 30 |
| app/Http/Middleware/PreventRequestsDuringMaintenance.php | 17 |
| app/Http/Middleware/RedirectIfAuthenticated.php | 32 |
| app/Http/Middleware/TrimStrings.php | 19 |
| app/Http/Middleware/TrustHosts.php | 20 |
| app/Http/Middleware/TrustProxies.php | 28 |
| app/Http/Middleware/VerifyCsrfToken.php | 17 |

Related, read for context but outside the assigned file list:
- app/Http/Kernel.php (middleware registration)
- app/Providers/RouteServiceProvider.php (HOME const, route group loading)
- app/Providers/AuthServiceProvider.php (policy registration)
- config/auth.php (guards/providers/password broker)
- database/migrations/2014_10_12_000000_create_users_table.php (users schema)
- routes/web.php:1-107, routes/api.php:1-19 (route declarations)
- docs/audit/routes.json (route list dump)
- resources/views/content/authentications/auth-login-basic.blade.php
- resources/views/content/authentications/auth-register-basic.blade.php
- resources/views/content/authentications/auth-forgot-password-basic.blade.php

## Classes & Responsibilities

### `App\Http\Controllers\authentications\LoginBasic` (app/Http/Controllers/authentications/LoginBasic.php:9)
- Namespace: `App\Http\Controllers\authentications` (lowercase `authentications` — PSR-4 dir/namespace both lowercase, consistent with folder name at app/Http/Controllers/authentications/).
- Extends: `App\Http\Controllers\Controller` (app/Http/Controllers/authentications/LoginBasic.php:9).
- No traits, no constructor-injected dependencies (uses `Auth` facade directly).
- `index()` (LoginBasic.php:11-14): returns `content.authentications.auth-login-basic` view. No dependencies.
- `login(Request $request)` (LoginBasic.php:16-31): validates `email` (required|email) and `password` (required) inline (LoginBasic.php:18-21); builds `$credentials` from `email`/`password` only (LoginBasic.php:23); calls `Auth::attempt($credentials)` (LoginBasic.php:25) — does **not** pass the `remember` checkbox value from the login form; on success redirects to `intended('/')` with a `withSuccess` flash (LoginBasic.php:26-27); on failure redirects back to `/auth/login-basic` also using `withSuccess` for the failure message (LoginBasic.php:30) — wrong flash-helper semantics for an error case.
- Dead code: commented `//dd($request);` (LoginBasic.php:17).

### `App\Http\Controllers\authentications\LogoutBasic` (app/Http/Controllers/authentications/LogoutBasic.php:10)
- Extends `Controller`. Imports global-namespace facades `Session` and `Auth` (no `use Illuminate\Support\Facades\...`, i.e. relies on root-namespace facade aliases) (LogoutBasic.php:7-8).
- `logout()` (LogoutBasic.php:12-18): calls `Session::flush()` then `Auth::logout()`, redirects to `/auth/login-basic`. No CSRF protection possible because the route is `GET` (see Smells).

### `App\Http\Controllers\authentications\RegisterBasic` (app/Http/Controllers/authentications/RegisterBasic.php:13)
- Extends `Controller`. Imports `Auth` facade (unused — RegisterBasic.php:7 imported but never referenced in the class body), `App\Models\User`, global `Session` (unused) and global `Hash`.
- `index()` (RegisterBasic.php:15-18): returns `content.authentications.auth-register-basic` view.
- `register(Request $request)` (RegisterBasic.php:20-39): validates `username` (required|unique:users), `email` (required|email|unique:users), `password` (required|min:6) (RegisterBasic.php:23-27); creates a `User` with `name => $request->username`, `email`, `password => Hash::make(...)` (RegisterBasic.php:30-34); redirects to `/` without logging the new user in and without email verification.
- Dead code: `//dd($request->all());` (RegisterBasic.php:22), `//dd($user);` (RegisterBasic.php:36).

### `App\Http\Controllers\authentications\ForgotPasswordBasic` (app/Http/Controllers/authentications/ForgotPasswordBasic.php:8)
- Extends `Controller`. Only one method.
- `index()` (ForgotPasswordBasic.php:10-13): returns `content.authentications.auth-forgot-password-basic` view. No `sendResetLink`/`reset` action exists anywhere in this class or elsewhere in `app/Http/Controllers/authentications` (confirmed by directory listing — only these 4 files exist).

### `App\Models\User` (app/Models/User.php:13)
- Namespace `App\Models`. Extends `Illuminate\Foundation\Auth\User as Authenticatable` (User.php:9,13).
- Traits: `HasApiTokens` (Laravel Sanctum), `HasFactory`, `Notifiable`, `SoftDeletes`, `SoftCascadeTrait` (askedio/laravel-soft-cascade) (User.php:15).
- `$fillable = ['name','email','password']` (User.php:22-26).
- `$hidden = ['password','remember_token']` (User.php:33-36).
- `$casts = ['email_verified_at' => 'datetime']` (User.php:43-45).
- No relations, no scopes, no accessors/mutators, no custom methods, no `MustVerifyEmail` implementation (imported at User.php:6 but the interface is **not implemented** on the class — dead import; email verification is therefore not enforced anywhere).
- No `$softCascade` property configured for `SoftCascadeTrait`, so its cascading-delete relation list is empty/undefined by this file alone (Open Question).

### Middleware
- `App\Http\Middleware\Authenticate` (Authenticate.php:7): extends Laravel's `Authenticate`; overrides `redirectTo()` to send unauthenticated non-JSON requests to `route('login')` (Authenticate.php:15-19), which resolves to `/auth/login-basic` (routes/web.php:85). Registered as the `auth` route-middleware alias (app/Http/Kernel.php:58).
- `App\Http\Middleware\RedirectIfAuthenticated` (RedirectIfAuthenticated.php:10): standard Laravel `guest` middleware; redirects any already-authenticated guard to `RouteServiceProvider::HOME` (`/home`, RouteServiceProvider.php:20) — **this constant does not match any registered route** (root route is `/`, named `dashboard-analytics`, routes/web.php:47; no `/home` route exists in routes/web.php or docs/audit/routes.json). Registered as `guest` alias (Kernel.php:62) but **not applied to any auth route** — see Smells.
- `App\Http\Middleware\EncryptCookies`, `TrimStrings` (excludes `password`, `current_password`, `password_confirmation`, TrimStrings.php:15-17), `TrustHosts`, `TrustProxies`, `VerifyCsrfToken` (no exceptions, VerifyCsrfToken.php:14-16), `PreventRequestsDuringMaintenance` — all thin stock Laravel subclasses with no custom behavior, registered in `$middleware`/`$middlewareGroups` (Kernel.php:16-24, 31-48).
- `App\Http\Middleware\LocaleMiddleware` (LocaleMiddleware.php:8-30): reads `session('locale')` against a hard-coded `$availLocale = ['en'=>'en','fr'=>'fr','ar'=>'ar']` array (LocaleMiddleware.php:20) and calls `app()->setLocale(...)` (LocaleMiddleware.php:25); registered globally in the `web` group (Kernel.php:40), runs on every web request including the auth pages.

## Data Flow (entrypoint -> exit)

**Login**
1. `GET /auth/login-basic` (routes/web.php:85, name `login`) → `LoginBasic@index` → view `auth-login-basic.blade.php`, form posts to `/auth/login-action` (auth-login-basic.blade.php:29).
2. `POST /auth/login-action` (routes/web.php:88, middleware: `web` only per docs/audit/routes.json) → `LoginBasic@login` → inline `$request->validate()` → `Auth::attempt($credentials)` → success: `redirect()->intended('/')`; failure: `redirect('/auth/login-basic')`.
3. `/` (routes/web.php:47) is protected by `auth` middleware and resolves to `dashboard\Analytics@index2` (outside this scope).

**Logout**
1. `GET /auth/logout` (routes/web.php:90, name `auth-logout`, middleware `web` only) → `LogoutBasic@logout` → `Session::flush()` + `Auth::logout()` → redirect to `/auth/login-basic`.

**Register**
1. `GET /auth/register-basic` (routes/web.php:86) → `RegisterBasic@index` → view, form posts to `/auth/register-action` (auth-register-basic.blade.php:30), field name `username` (auth-register-basic.blade.php:34).
2. `POST /auth/register-action` (routes/web.php:87, middleware `web` only) → `RegisterBasic@register` → validate → `User::create()` → redirect `/` (user is **not authenticated** after this redirect, and `/` requires `auth`, so the user lands back at the login-redirect flow — net effect: register does not log the user in).

**Forgot password**
1. `GET /auth/forgot-password-basic` (routes/web.php:89) → `ForgotPasswordBasic@index` → view only.
2. The view's form has `action="javascript:void(0)"` and `method="GET"`, no `@csrf`, no submit handler visible in this file (auth-forgot-password-basic.blade.php:28-34) → **dead end, no server-side handler exists** for password reset submission anywhere in `app/Http/Controllers/authentications/`.

**Locale/theme toggles** (session-only, no controller class): `GET /theme/{theme}` and `GET /lang/{lang}` are declared inside `Route::group(['middleware'=>['auth']], ...)` (routes/web.php:33-44), confirmed in docs/audit/routes.json (`"uri":"lang\/{lang}"` → middleware `["web","App\\Http\\Middleware\\Authenticate"]`). Both are anonymous closures, not classes in this scope, but they run through `LocaleMiddleware` and `auth` on every request.

**Sanctum**: `GET /api/user` (routes/api.php:17-19) uses `auth:sanctum` middleware; `HasApiTokens` trait on `User` (User.php:15) is the only wiring — no token-issuing endpoint exists in this scope (Open Question).

## External Dependencies (packages, APIs, queues)

- `laravel/framework` `^9.0` (composer.json) — Auth facade, session guard.
- `laravel/sanctum` `^2.14` (composer.json) — `HasApiTokens` trait on `User`; `EnsureFrontendRequestsAreStateful` is commented out in the `api` middleware group (app/Http/Kernel.php:44), only `auth:sanctum` token guard is effectively usable, not SPA cookie auth.
- `askedio/laravel-soft-cascade` `^10.0` (composer.json) — `SoftCascadeTrait` on `User` (User.php:15); no `$softCascade` property found on the model and no config file located under `config/` (searched, no match) — cascade behavior is unconfigured/unknown (Open Question).
- `fruitcake/laravel-cors` `^2.0.5` (composer.json) — `HandleCors` in global middleware (Kernel.php:19).
- No external identity providers, no queue/notification usage observed in this scope (`Notifiable` trait is present on `User`, User.php:15, but no notification classes were found referencing it within the read files).

## Smells & Debt

| file | line | issue | severity |
|---|---|---|---|
| app/Http/Controllers/authentications/RegisterBasic.php | 24 | Validates `'username' => 'required|unique:users'`, but the `users` table (database/migrations/2014_10_12_000000_create_users_table.php:16-25) has no `username` column — Laravel's `unique` rule defaults to checking a column matching the field name, so this will raise a `QueryException` (unknown column `username`) on every registration attempt. Likely-breaking bug. | 5 |
| app/Http/Controllers/authentications/LoginBasic.php, RegisterBasic.php | 88, 87 (routes/web.php) | No `throttle` middleware on `/auth/login-action` or `/auth/register-action` (confirmed via docs/audit/routes.json: middleware list is just `["web"]`) — no brute-force/credential-stuffing rate limiting on login or registration. | 4 |
| routes/web.php | 85-90 | None of the 4 `auth/*` routes carry the `guest` middleware (`RedirectIfAuthenticated`), so an already-authenticated user can still load/submit the login, register and forgot-password pages. | 3 |
| app/Providers/RouteServiceProvider.php | 20 | `HOME = '/home'` is used by `RedirectIfAuthenticated` (RedirectIfAuthenticated.php:26) but no `/home` route exists anywhere in routes/web.php or docs/audit/routes.json — would 404 if this middleware were ever reached (currently unreachable, see row above, which compounds the risk if `guest` is added later without fixing this constant). | 3 |
| app/Http/Controllers/authentications/LoginBasic.php | 25-30 | `Auth::attempt($credentials)` never receives the `remember` form field (auth-login-basic.blade.php:49 posts `name="remember"`), so the "Remember Me" checkbox is non-functional (always false). | 2 |
| app/Http/Controllers/authentications/LoginBasic.php | 30 | Failure path uses `->withSuccess(__('Login details are not valid'))` — an error message flashed under a "success" key, likely a UI/copy bug in the Blade layer consuming this flash. | 2 |
| app/Http/Controllers/authentications/ForgotPasswordBasic.php | (whole class) | Only renders a view; no corresponding "send reset link" or "reset password" controller/route exists anywhere under `app/Http/Controllers/authentications/`, and the Blade form (auth-forgot-password-basic.blade.php:28) posts nowhere (`action="javascript:void(0)"`, method GET, no `@csrf`) — password reset is entirely non-functional despite `config/auth.php:89-96` defining a full `passwords.users` broker config. | 4 |
| app/Http/Controllers/authentications/RegisterBasic.php | 38 | After successful registration the new user is not authenticated (no `Auth::login($user)`) and is redirected to `/` which requires `auth` middleware (routes/web.php:47) — user is bounced back to a login flow immediately after signing up. | 3 |
| app/Http/Controllers/authentications/LogoutBasic.php | 12-18 | Logout is a `GET` route (routes/web.php:90) performing a state-changing action; GET logout is CSRF-exposed (an attacker page can force-logout a victim via `<img src>`), and mixes `Session::flush()` (wipes entire session, including flash/locale/theme data) with `Auth::logout()`. | 3 |
| app/Models/User.php | 6 | `use Illuminate\Contracts\Auth\MustVerifyEmail;` is imported but the interface is never implemented on the class — email verification (`email_verified_at` cast exists at User.php:44) is not enforced by any middleware (`verified` alias registered at Kernel.php:66 but unused in routes.json) and there is no verification-sending logic in this scope. | 2 |
| app/Http/Controllers/authentications/LogoutBasic.php | 7-8 | Uses bare global facades `Session` / `Auth` instead of `Illuminate\Support\Facades\*` imports, inconsistent with the `Illuminate\Support\Facades\Auth` style used in LoginBasic.php:7 — style/maintainability inconsistency, not a bug (root-namespace aliases resolve fine by default Laravel config). | 1 |
| app/Providers/AuthServiceProvider.php | 15-17 | `$policies` array is empty — no authorization Policies exist for any model in the app (confirmed: no `app/Policies` matches surfaced during this audit) — all access control in the app is reduced to "authenticated or not" via the `auth` middleware, no per-resource authorization layer. | 3 |
| app/Models/User.php | 15 | `SoftCascadeTrait` (askedio/laravel-soft-cascade) applied with no discoverable `$softCascade` property on this model and no package config file found under `config/` — cascading soft-delete behavior for related records (e.g. `Debt.user_id`, app/Models/Debt.php:15) could be silently absent or silently configured elsewhere; unverifiable from files in scope. | 2 |
| routes/web.php | 92-105 | Two unauthenticated, unnamed closures (`list/debt/supplier/` and `password/hash`) sit directly below the `authentication` route block with no middleware and no controller — `password/hash` hashes a hard-coded literal `'123456789'` and returns it in plaintext response (routes/web.php:101-105). Not part of the assigned file scope (controllers/model/middleware only) but discovered while reading routes/web.php for the auth routes; flagging since it's adjacent, publicly reachable, and auth-relevant (exposes a working `Hash::make` oracle). | 3 |

## Open Questions

1. Does `askedio/laravel-soft-cascade`'s cascade list for `User` live in a config file not matched by my search (I searched `config/*.php` for `SoftCascade` and found nothing), a service provider, or is it genuinely unconfigured? Unresolved from files in scope.
2. Is there a Sanctum token-issuing endpoint anywhere outside this scope (e.g., a mobile/API login controller)? None found in `app/Http/Controllers/authentications/` or `routes/api.php` (only `/api/user` read-endpoint exists, routes/api.php:17-19).
3. Is the commented-out "Create an account" link in `auth-login-basic.blade.php:60-65` intentional (registration disabled from the login page while the route itself remains live), or leftover dead markup?
4. Is `RouteServiceProvider::HOME = '/home'` (RouteServiceProvider.php:20) meant to be updated to `/` to match the actual dashboard route, or is `/home` a route that was removed and the constant never updated? Cannot determine intent from files in scope.
5. Given the `username` column does not exist on `users` (see Smells row, severity 5), is there an unrun/missing migration intended to add it, or was `RegisterBasic` never exercised against the current schema? Cannot confirm without migration history/CI logs, which are outside this scope.

docs/audit/raw/05-auth-access.md
