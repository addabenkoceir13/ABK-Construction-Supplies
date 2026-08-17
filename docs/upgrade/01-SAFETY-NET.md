# 01 — Characterization Safety Net

Baseline test suite for the Laravel 9 → 13 upgrade on branch `upgrade/l9-to-l13`.

These are **characterization tests**: they encode what the app *actually does today*,
not what it should do. Several assertions pin behaviour that is plainly wrong — those
are marked `// SMELL:` in the test source and listed under
[Known-bad behaviour pinned as-is](#known-bad-behaviour-pinned-as-is). Do not "fix" a
test to make it pass during the upgrade; if one goes red, the upgrade changed
behaviour and that needs a deliberate decision.

**Status:** 63 tests, 63 passing.

---

## Database isolation

Tests must never touch `dettessofiane` (the real dev schema). Three independent layers
enforce this:

1. **A dedicated connection.** `config/database.php` defines `mysql_testing`, which
   reuses the host/user/password of `mysql` but takes its schema name from
   `DB_TEST_DATABASE` (default `dettessofiane_test`).
2. **phpunit.xml selects it.** `DB_CONNECTION=mysql_testing` and
   `DB_TEST_DATABASE=dettessofiane_test`.
3. **A runtime guard.** `Tests\TestCase::setUp()` aborts the entire run if the active
   schema is on a forbidden list (`dettessofiane`) or does not end in `_test`. This
   fires *before* `RefreshDatabase` can migrate or truncate anything.

The guard was verified by temporarily repointing `phpunit.xml` at `dettessofiane`; the
run failed with `REFUSING TO RUN: tests are pointed at the protected database
"dettessofiane"`.

`php artisan migrate --database=mysql_testing` was run once against
`dettessofiane_test` (15 migrations, 14 tables). `dettessofiane` was never migrated.

> `.env` is off-limits and was not modified. The test connection inherits
> `DB_USERNAME`/`DB_PASSWORD` from it.

### Gotcha: auto-increment ids under RefreshDatabase

`RefreshDatabase` wraps each test in a transaction and rolls it back, but **InnoDB does
not rewind its auto-increment counter on rollback**. Fixtures that must occupy a
specific id therefore set it explicitly. This matters because
`DebtRepository::debtUnPaid()`/`debtPaid()` and the public `/list/debt/supplier/` route
are hardcoded to the literal `tractor_driver_id = 1`. See
`Tests\Concerns\SeedsDomainFixtures::normalDriver()`.

---

## Factories

`UserFactory` had a hardcoded `email` (`addasofiane@gmail.com`), which made it
impossible to build a second user against the unique index. It now uses faker, with a
shared `UserFactory::PASSWORD` constant so login tests know the plaintext.

Nine factories were added: `Debt`, `DebtProduct`, `DebtHistory`, `TractorDriver`,
`Category`, `SubCategory`, `FuelStation`, `Vehicle`, `InsuranceVehicle`.

Two schema landmines documented in the factory source:

- `debt_products.status` is `enum('1','0')`. Passing an **integer** `0` makes MySQL
  interpret it as an enum *index*, which truncates under strict mode. Must be `'0'`.
- `debt_histories.amount` is `decimal(8,2)` (max 999 999.99) while
  `debts.total_debt_amount` is `decimal(20,2)`. A large payment overflows the history row.

---

## Covered routes

| Route | Method | Role | What is asserted |
|---|---|---|---|
| `/auth/login-action` | POST | guest | 302 → `/`, `assertAuthenticatedAs`, guard session key present |
| `/auth/login-action` | POST | guest | session id is rotated on login |
| `/auth/login-action` → `/` | POST+GET | guest→auth | auth survives the redirect, dashboard 200 |
| `/auth/login-action` | POST | guest | wrong password → 302 to login, stays guest, no guard key |
| `/auth/login-action` | POST | guest | unknown email → 302 to login, stays guest |
| `/auth/login-action` | POST | guest | malformed input → errors on `email`,`password`, stays guest |
| `/auth/login-action` | POST | guest | empty submission → errors on both fields |
| `/auth/logout` | GET | auth | 302 → login, guest, **entire** session flushed (`theme` + unrelated keys gone) |
| `/auth/logout` | GET | auth | succeeds over GET with no CSRF token |
| 11 protected routes | GET/POST | guest | 302 → `/auth/login-basic` (breakage canary) |
| `/auth/login-basic` | GET | guest | 200, anchor `تسجيل الدخول الأساسي` |
| `/auth/register-action` | POST | guest | **500**, no user created, stays guest |
| `/auth/register-basic` | GET | guest | 200 |
| `/debt` | POST | auth | +1 debt, +2 products, total/rest derived, `debt_paid` NULL, 302 → `/debt` |
| `/debt` | POST | auth | invalid → errors, zero rows written |
| `/debt/pays/{id}` | PATCH | auth | exact payment → `paid`, rest 0, +1 history row |
| `/debt/pays/{id}` | PATCH | auth | partial → stays `unpaid`, rest 600, `date_end_debt` still stamped |
| `/debt/pays/{id}` | PATCH | auth | second partial settles it, 2 history rows |
| `/debt/pays/{id}` | PATCH | auth | overpayment refused, no history, **line items already flagged paid** |
| `/debt/pays/{id}` | PATCH | auth | `'1e3'` settles a 1000.00 debt (loose `==`) |
| `/debt/pays/{id}` | PATCH | auth | missing amount → silently swallowed, no history |
| `/debt/{id}` | DELETE | auth | soft-deleted, 302 → `/debt` |
| `/debt/{id}` | PUT | auth | totals recomputed from line items |
| `/debt/search` | POST | auth | 200 JSON, matching `fullname`/`phone` fragment |
| `/fuel-stations` | POST | auth | +1 receipt, columns written, defaults to `unpaid` |
| `/fuel-stations` | POST | auth | unknown `vehicle_id` → error, nothing written |
| `/fuel-stations/change-status` | POST | auth | only the listed ids flip to `paid` |
| `/fuel-stations/status/{id}` | PATCH | auth | status set from request |
| `/fuel-stations/{id}` | DELETE | auth | soft-deleted |
| `/services/building-materals` | POST | auth | row **is** created but redirect goes *back*, not to the index |
| `/services/building-materals` | POST | auth | empty name → error, nothing written |
| `/services/building-materals/{id}` | PUT | auth | rename persisted |
| `/services/tractor-driver` | POST | auth | `type`/`status` mass-assigned unvalidated |
| `/services/tractor-driver` | POST | auth | non-numeric phone → error, nothing written |
| `/services/tractor-driver/{id}` | DELETE | auth | soft-deleted |
| `/services/vehicle` | POST | auth | +1 vehicle +1 insurance, plate composed `12345 - 2020 - 16` |
| `/services/vehicle/{id}/added-date` | POST | auth | appends an insurance row |
| `/services/vehicle/{id}/added-date` | POST | auth | missing `end_date` → error, nothing written |
| `/services/vehicle/{id}` | DELETE | auth | soft-deleted |
| `/` | GET | auth | **500 on an empty DB**; 200 once any data exists |
| `/debt` | GET | auth | 200, anchor `الديون`, lists driver-1 debts |
| `/debt/status/paid` | GET | auth | 200, excludes unpaid |
| `/fuel-stations` | GET | auth | 200, anchor `محاسبة مشتريات الوقود` |
| `/debt-supplier` | GET | auth | 200, lists supplier debts |
| `/services/*` indexes | GET | auth | 200 |
| `/list/debt/supplier/` | GET | **guest** | **200 — leaks debtor name + phone to anonymous visitors** |
| `/list/debt/supplier/` | GET | guest | excludes paid debts and driver id 1 |
| `/password/hash` | GET | **guest** | **200 — public bcrypt oracle** |

Assertions are limited to status code, redirect target, session keys, row-count deltas,
specific written columns, and 2–3 anchor strings. No timestamps, ids, full markup, or
unguaranteed ordering.

---

## Known-bad behaviour pinned as-is

Each is encoded in a passing test with a `// SMELL:` annotation. **None were fixed** —
this is characterization, not TDD.

1. **`/list/debt/supplier/` is public.** Declared outside the `auth` group. Renders every
   unpaid supplier debt — full names, phone numbers, balances — to anonymous visitors.
2. **`/password/hash` is public.** Dead developer scaffolding that returns a bcrypt hash
   of the hardcoded string `123456789`.
3. **Registration is entirely broken.** `RegisterBasic::register()` validates
   `'username' => 'unique:users'`, but the column is `name`. Every request dies with
   `Unknown column 'username'` → HTTP 500. No user can be created through the UI.
4. **Overpayment corrupts line items.** `DebtController::payDebt()` flags every
   `id_debt_product` as paid at the top of the method, *before* the overpayment guard
   further down rejects the payment and early-returns. There is no rollback on that
   path, so line items end up marked paid against a debt that received no money.
5. **Loose `==` on money.** `payDebt()` compares decimal strings with `==`, so PHP's
   numeric-string juggling applies: `'1e3'` settles a 1000.00 debt in full.
6. **`payDebt()` has no validation at all.** Unlike `store()`/`update()` it runs no
   `Validator`. A missing amount produces a NULL history amount, the insert violates
   NOT NULL, the `catch` swallows it, and the user is redirected back with no error.
7. **Partial payments stamp `date_end_debt`.** A debt that is still `unpaid` gets a
   "closed on" date, so any report keying off `date_end_debt` sees it as settled.
8. **`debt_paid` is left NULL on create** rather than `0.00`, so the payment arithmetic
   relies on null-to-zero coercion.
9. **`CategoryController` redirects to a route name that does not exist.**
   `route('building-materals.index')` — the resource is registered as
   `services.building-materials`. `route()` throws, the `try/catch` swallows it, and the
   user gets a *failure* toast for an operation that actually **succeeded**.
10. **Tractor-driver mass assignment.** `store()` validates only `fullname`+`phone` but
    persists `$request->all()`. A client can set `type=normal` — the exact flag the debt
    listing keys off — or `status=blocked`.
11. **Logout flushes the whole session.** `Session::flush()` runs *before*
    `Auth::logout()`, destroying unrelated keys including `theme`/`locale`.
12. **Logout is a GET route with no CSRF token**, so it can be triggered cross-site.
13. **The dashboard 500s on an empty database.** `index2.blade.php` divides by totals
    that are 0, raising `Division by zero` — a fatal `DivisionByZeroError` in PHP 8
    where PHP 7 only warned. A fresh install cannot render its own home page.
14. **Failed login flashes a `success` key.** `LoginBasic::login()` reports the failure
    via `->withSuccess('Login details are not valid')`.

---

## Deliberately NOT asserted

- **The `success` flash value.** After both a successful and a failed login the key is
  unreadable — only the `_flash.old` marker survives. Tests run `SESSION_DRIVER=array`
  while the app runs `file`, so this could not be separated from a test-environment
  artifact. Asserting it would risk a false failure during the upgrade. Worth a
  dedicated investigation outside the upgrade.
- Timestamps, generated ids, full rendered markup, and any ordering the query does not
  guarantee.

---

## Uncovered critical routes

| Route | Why not covered |
|---|---|
| `/debt-supplier` write actions (`POST`/`PUT`/`DELETE`, `debt-supplier/pays`) | `DebtWithSupplierController` mirrors `DebtController`; the read path is covered and the money-path smells are already pinned on the `DebtController` side. Worth adding if supplier debts are edited in practice. |
| `/fuel-stations/search` (`indexA`) | DataTables server-side endpoint; only meaningful under an AJAX payload and its response shape is presentation data, not an upgrade-stable contract. |
| `/print/printer-facteur/{debt}/{fullname}` | Invoice rendering — output is markup, which the brief excludes from assertions. |
| `/theme/{theme}`, `/lang/{lang}` | Both `redirect()->back()` session setters; the `LocaleMiddleware` behaviour they drive is cosmetic. |
| `/services/subcategory` resource | `SubCategoryController` `store`/`update`/`destroy` are empty stubs — there is no behaviour to characterize. Only `show()` does anything. |
| `/api/user` (`auth:sanctum`) | Sanctum's stateful middleware is commented out in `Kernel.php`; no token flow exists in the app today. |

---

## Breakage detection self-check

A suite that never goes red is not a safety net. Verified by commenting out the `auth`
alias in `app/Http/Kernel.php`:

```php
// 'auth' => \App\Http\Middleware\Authenticate::class,
```

**Result: 45 failed, 18 passed** (from 63 passing).

The canary fired exactly as designed — all 11 data sets of
`AuthenticationTest::test_guest_is_redirected_from_protected_routes` went red, since the
missing alias turns the guest 302 into an unresolved-middleware exception. The blast
radius also took down every `actingAs()` test behind the `auth` group, which is itself
useful signal: it shows the suite's coverage of protected routes is real and not
incidentally passing.

`app/Http/Kernel.php` was then restored (`git diff` reports it byte-identical to HEAD)
and the suite returned to **63 passed**.

---

## Running the suite

```bash
php artisan test
```

The dedicated schema must exist first:

```sql
CREATE DATABASE dettessofiane_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
