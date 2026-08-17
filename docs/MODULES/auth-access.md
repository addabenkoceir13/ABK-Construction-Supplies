# Module: Auth & Access

Session-based login/register/logout on Laravel's default `Authenticatable` guard. There is no authorization
layer beyond "authenticated or not" — see `ARCHITECTURE.md` Wall 3 and §5.5 for why this is one of the
riskiest parts of the app.

## Files

| File | LOC |
|---|---:|
| `app/Http/Controllers/authentications/LoginBasic.php` | 32 |
| `app/Http/Controllers/authentications/LogoutBasic.php` | 19 |
| `app/Http/Controllers/authentications/RegisterBasic.php` | 40 |
| `app/Http/Controllers/authentications/ForgotPasswordBasic.php` | 14 |
| `app/Models/User.php` | 46 |
| `app/Http/Middleware/Authenticate.php` | 21 |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | 32 |
| `app/Http/Middleware/LocaleMiddleware.php` | 30 |
| (5 more stock middleware files: `EncryptCookies`, `PreventRequestsDuringMaintenance`, `TrimStrings`, `TrustHosts`, `TrustProxies`, `VerifyCsrfToken`) | — |

Namespace/directory `app/Http/Controllers/authentications` is lowercase (unusual but internally consistent).

## Responsibilities

### `LoginBasic` (`app/Http/Controllers/authentications/LoginBasic.php:9`)
`login(Request $request)` (`:16-31`) validates `email`/`password` inline, calls `Auth::attempt($credentials)`
— **does not read the `remember` checkbox** from the login form, so "Remember Me" is permanently non-functional.
On success: `redirect()->intended('/')`. On failure: redirects back using `->withSuccess(...)` for an *error*
message (`:30`) — wrong flash-key semantics, likely a UI copy bug downstream.

### `LogoutBasic` (`app/Http/Controllers/authentications/LogoutBasic.php:10`)
`logout()` (`:12-18`): `Session::flush()` + `Auth::logout()`. Registered as a **`GET`** route
(`routes/web.php:90`) — a state-changing action reachable without CSRF protection (an attacker page can
force-logout a victim via `<img src>`). `Session::flush()` also wipes theme/locale/flash data along with auth.

### `RegisterBasic` (`app/Http/Controllers/authentications/RegisterBasic.php:13`)
`register(Request $request)` (`:20-39`) validates `username => 'required|unique:users'` — **the `users` table
has no `username` column** (`database/migrations/2014_10_12_000000_create_users_table.php:16-25`). Laravel's
`unique` rule checks a column matching the field name by default, so this raises a `QueryException` (unknown
column) on **every** registration attempt. After a successful `User::create()`, the new user is **not logged
in** (`Auth::login()` never called) and is redirected to `/`, which requires `auth` — the user bounces
straight back into the login flow.

### `ForgotPasswordBasic` (`app/Http/Controllers/authentications/ForgotPasswordBasic.php:8`)
Renders a view only. **No `sendResetLink`/`reset` action exists anywhere** in this class or elsewhere in
`app/Http/Controllers/authentications/` — the form's `action="javascript:void(0)"`, method `GET`, no `@csrf`
confirms it: password reset is entirely non-functional, despite `config/auth.php:89-96` defining a complete
`passwords.users` broker configuration that nothing calls.

### `User` model (`app/Models/User.php:13`)
Traits: `HasApiTokens` (Sanctum), `HasFactory`, `Notifiable`, `SoftDeletes`, `SoftCascadeTrait`. `$fillable =
['name','email','password']`. `$casts = ['email_verified_at' => 'datetime']` — **the only model in the
codebase with any `$casts` declared at all**. Imports `MustVerifyEmail` but **does not implement it** — email
verification is not enforced anywhere (`verified` middleware alias registered but unused in any route). No
relations to any domain model, despite `debts.user_id` existing as a foreign key — see `DOMAIN-MODEL.md`.

### Middleware
`Authenticate` (`app/Http/Middleware/Authenticate.php:15-19`) overrides `redirectTo()` → `route('login')` →
`/auth/login-basic`. `RedirectIfAuthenticated` (`guest` alias) redirects already-authenticated users to
`RouteServiceProvider::HOME` = `/home` — **a route that does not exist anywhere in the app** (root is `/`,
named `dashboard-analytics`). This middleware is registered but **applied to zero routes** — none of the 4
`auth/*` routes carry `guest`, so an already-logged-in user can still load/submit login/register/forgot-password.
`LocaleMiddleware` (`app/Http/Middleware/LocaleMiddleware.php:8-30`) runs globally in the `web` group, reading
`session('locale')` against a hardcoded `['en','fr','ar']` map.

## Data flow

**Login:** `GET /auth/login-basic` → view, posts to `POST /auth/login-action` (middleware: `web` only, no
`throttle`) → `LoginBasic::login` → `Auth::attempt` → success `redirect()->intended('/')`; `/` requires `auth`
and resolves to `Analytics@index2` (dashboard module).

**Logout:** `GET /auth/logout` (no CSRF protection possible on a GET route) → `Session::flush()` + `Auth::logout()`.

**Register:** `POST /auth/register-action` (no `throttle`) → validation crashes on the `username` column (see
above) before a user is ever created, in the current schema.

**Sanctum:** `GET /api/user` (`routes/api.php:17-19`) is the only API route, guarded by `auth:sanctum`. No
token-issuing endpoint exists anywhere in scope — `HasApiTokens` is wired but nothing mints tokens.

**Adjacent, discovered while tracing routes (not auth-controller code, but auth-relevant):** two closures at
`routes/web.php:92-105` sit outside every middleware group — `GET list/debt/supplier/` (public debt data leak,
see `MODULES/debt-billing.md`) and `GET password/hash` (hashes the hardcoded string `'123456789'` with bcrypt
and echoes the result to any anonymous caller — a leftover dev utility route, live in what `config/app.php:31`
defaults to a `production` environment).

## Known issues (severity 4-5)

| Issue | Where | Severity |
|---|---|---|
| Registration validates a `username` column that doesn't exist on `users` — breaks every signup | `RegisterBasic.php:24` | 5 |
| No `throttle` middleware on login or register — no brute-force/credential-stuffing protection | `routes/web.php:87-88` | 4 |
| No password-reset handler exists despite a full broker config | `ForgotPasswordBasic.php` (whole class) | 4 |
| `RouteServiceProvider::HOME = '/home'` points at a route that doesn't exist | `RouteServiceProvider.php:20` | 3 |
| Logout is a state-changing GET route, CSRF-exposed | `LogoutBasic.php:12-18`, `routes/web.php:90` | 3 |
| New users aren't logged in after registering, and are redirected into an `auth`-gated route | `RegisterBasic.php:38` | 3 |
| `guest` middleware registered but applied to zero routes | `routes/web.php:85-90` | 3 |
| `$policies` empty, `app/Policies` absent — no authorization layer beyond `auth`/not | `AuthServiceProvider.php:15-17` | 3 |

Full list with line numbers: `docs/audit/raw/05-auth-access.md`.

## Open questions

- Is `askedio/laravel-soft-cascade`'s cascade list for `User` configured anywhere not matched by search, or
  genuinely unconfigured?
- Is there a Sanctum token-issuing endpoint outside this scope (mobile/API login controller)? None found in
  `routes/api.php` beyond the read-only `/api/user`.
- Is `RouteServiceProvider::HOME` meant to be updated to `/`, or is `/home` a route that was removed without
  updating the constant? Not determinable from files in scope.
- Given the `username` column doesn't exist, was there an unrun/missing migration intended to add it, or was
  `RegisterBasic` never exercised against the current schema? Needs migration history/CI logs to confirm.
