# Module: Debt & Billing

Materials sold on credit. Two parallel UI modules — **Debt** (walk-in clients) and **DebtSupplier**
(suppliers/tractor-drivers) — operate on the *same* `debts` table, discriminated only by
`tractor_driver_id = 1` (client) vs `!= 1` (supplier). See `DOMAIN-MODEL.md` for the schema and
`ARCHITECTURE.md` §3 for why these are "one module forked into two," not two modules.

## Files

| File | LOC | Role |
|---|---:|---|
| `app/Http/Controllers/Debt/DebtController.php` | 383 | Client-debt CRUD + payment |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 367 | Supplier-debt CRUD + payment (~95% duplicate of the above) |
| `app/Http/Controllers/Print/PrinterController.php` | 23 | Invoice print view |
| `app/Models/Debt.php` | 75 | Entity + reporting aggregates |
| `app/Models/DebtHistory.php` | 24 | Payment audit trail |
| `app/Models/DebtProduct.php` | 33 | Line item |
| `app/Repositories/Debt/{DebtRepository,EloquentDebt}.php` | 50 / 99 | Repository pair |
| `app/Repositories/DebtHistory/{DebtHistoryRepository,EloquentDebtHistory}.php` | 40 / 79 | Repository pair |
| `app/Repositories/DebtProduct/{DebtProductRepository,EloquentDebtProduct}.php` | 40 / 74 | Repository pair |
| `resources/views/content/Printer/facteur-client.blade.php` | 172 | Printable invoice |

`resources/views/content/Debt/*.blade.php` and `DebtWithSupplier/*.blade.php` were audited from the
frontend-views scope, not this one — see `docs/audit/raw/08-frontend-views.md`.

## Responsibilities

### `DebtController` (`app/Http/Controllers/Debt/DebtController.php:19`)
Injects `DebtRepository, DebtHistoryRepository, DebtProductRepository, CategoryRepository,
TractorDriverRepository` (`:27-34`).

| Method | Line | Behavior |
|---|---|---|
| `index()` | `:36` | Unpaid client debts via `debtUnPaid()` (hardcodes `tractor_driver_id = 1`) |
| `indexPaid()` | `:48` | Paid client debts via `debtPaid()` |
| `store()` | `:76` | Validates header only; opens transaction; creates `Debt`; loops parallel request arrays to create `DebtProduct` rows; sums into `total_debt_amount`/`rest_debt_amount`; commits |
| `show()` / `edit()` | `:152` / `:164` | Load one debt, no eager-loading |
| `update()` | `:181` | Same shape as `store()`, branches create-vs-update per line item on `$idOld == 0` (loose comparison) |
| `destroy()` | `:282` | Soft-deletes. **No `DB::beginTransaction()`**, but the catch block calls `rollBack()` — throws on any error, masking the real one |
| `payDebt()` | `:296` | 4-branch if/elseif recomputing `debt_paid`/`rest_debt_amount`/`status`; writes a `DebtHistory` row |
| `searchName()` | `:371` | AJAX search; queries `Debt::where(...)` **directly**, bypassing the repository |

### `DebtWithSupplierController` (`app/Http/Controllers/Debt/DebtWithSupplierController.php:18`)
Same 5 injected repositories, near-identical method set on `tractor_driver_id != 1` rows via
`driverDebtUnPaid()`/`driverDebtPaid()`. Two confirmed copy-paste bugs redirect to the *client* route instead
of the supplier one: `destroy()` (`:292` → `route('debt.index')`) and `payDebt()`'s "amount exceeds" branch
(`:343` → `route('debt.index')`).

### `PrinterController` (`app/Http/Controllers/Print/PrinterController.php:9`)
`factuerClient($id, $fullname)` (`:17`) loads a debt by `$id` and renders the invoice. `$fullname` is
accepted but **never validated against the debt** — no ownership check ties the requesting user to the debt
being printed (IDOR: any authenticated user can print any debt by guessing `$id`).

### `Debt` model (`app/Models/Debt.php:10`)
`$softCascade = ['getDebtProduct']` (`:28`). No `$casts` — `total_debt_amount`, `debt_paid`,
`rest_debt_amount` are `decimal(20,2)` in the DB but arrive in PHP as strings. Four unfiltered static
aggregates (`getTotalDebt/getTotalPaidDebt/getTotalRestDebt`, `:40-52`, and `getDebtTimeline`, `:54-67`) sum
**both** client and supplier debt with no `tractor_driver_id` filter — see `MODULES/dashboard-analytics.md`.

### `DebtProduct` model (`app/Models/DebtProduct.php:10`)
`quantity` is a `string` DB column despite being numeric. `status` is `enum(1,0)` — coerces to `'1'`/`'0'`
strings, unconventional.

### `DebtHistory` model (`app/Models/DebtHistory.php:8`)
No `SoftDeletes` trait, though its migration creates a `deleted_at` column
(`database/migrations/2025_08_18_144113_create_debt_histories_table.php:22`) — `delete()` hard-deletes here
despite the schema supporting soft delete. `amount` is `decimal(8,2)`, smaller precision than every sibling
money column (`decimal(20,2)`) — caps at 999,999.99.

## Data flow

**Create a client debt:** `POST /debt` → `DebtController::store` → `Validator::make` (header fields only —
line items unvalidated) → `DB::beginTransaction()` → `DebtRepository->create()` → loop request arrays
(`name_product[]`, `quantity[]`, `amount_due[]`, `date_debt[]`, `subcategory_ids[]`) creating `DebtProduct`
rows → write back computed totals → `DB::commit()`.

**Record a payment:** `PATCH /debt/pays/{debt}` → `payDebt()` → flip selected `DebtProduct.status = 1` →
4-branch recompute of `debt_paid`/`rest_debt_amount`/`status` → write `DebtHistory` row → commit.

**Print invoice:** `GET /print/printer-facteur/{debt}/{fullname}` → `find($id)` (no eager load) → view loops
`$debt->getDebtProduct` and per-product `$item->getSubcategory->display_name` → N+1 (1 + N + N queries).

**Unauthenticated read path:** `GET /list/debt/supplier` (`routes/web.php:92-99`) is a route closure sitting
**outside** every `middleware(['auth'])` group. It queries `Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->get()`
directly and renders `content.Liste.index` — publicly exposes unpaid supplier debts (names, phones,
amounts) to anyone. See `ARCHITECTURE.md` Wall 3.

## Repository layer

`DebtRepository`/`EloquentDebt` (`app/Repositories/Debt/EloquentDebt.php`): `debtPaid()`/`debtUnPaid()`
hardcode `whereTractorDriverId(1)` (`:33-40`, note `'1'` string vs `1` int inconsistency between methods);
`driverDebtPaid()`/`driverDebtUnPaid()` hardcode `!= 1` (`:25-32`). `paginate($perPage, $search)` (`:87-98`)
does not paginate — calls `->get()` and returns a `Collection`, then conditionally calls `->appends(...)`,
which does not exist on `Collection` (throws `BadMethodCallException` if `$search` is ever truthy). Currently
unused by the in-scope controllers, but part of the public interface contract. `DebtHistoryRepository` and
`DebtProductRepository` repeat the identical broken `paginate()` pattern.

## Known issues (severity 4-5)

| Issue | Where | Severity |
|---|---|---|
| `dd($e->getMessage())` left in production `update()` catch block | `DebtController.php:270`, `DebtWithSupplierController.php:275` | 5 |
| View-path case mismatch: `content.debt.index` vs tracked `content/Debt/` | `DebtController.php:45,155,171` | 5 |
| Unauthenticated public route exposes supplier debt data | `routes/web.php:92-99` | 5 |
| `rollBack()` with no matching `beginTransaction()` in `destroy()` | both controllers | 4 |
| IDOR — no ownership check on invoice printing | `PrinterController.php:17-22` | 4 |
| `store()`/`update()`/`payDebt()` logic duplicated near-verbatim across Debt and DebtSupplier | both controllers | 3 |
| Line-item request arrays never validated before being looped/summed | both controllers' `store()`/`update()` | 3 |
| `destroy()` redirects to the wrong module's index (copy-paste) | `DebtWithSupplierController.php:292,343` | 3 |

Full list with line numbers: `docs/audit/raw/01-debt-billing.md`.

## Open questions

- Whether `Debt::getTotalDebt()` et al. mixing client and supplier debt is an intentional combined KPI or a
  bug — needs product input, not resolvable from code (`ARCHITECTURE.md` §7).
- Whether the case-mismatched view paths actually fail depends on the production filesystem's case
  sensitivity — unverified.
- Whether any authorization check for `PrinterController::factuerClient` exists beyond route-level `auth` —
  no Policy/Gate found repo-wide for `debt`.
