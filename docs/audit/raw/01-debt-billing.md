# Scope: Debt & Billing

## Files

| File | LOC |
|---|---|
| `app/Http/Controllers/Debt/DebtController.php` | 383 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 367 |
| `app/Http/Controllers/Print/PrinterController.php` | 23 |
| `app/Models/Debt.php` | 75 |
| `app/Models/DebtHistory.php` | 24 |
| `app/Models/DebtProduct.php` | 33 |
| `app/Repositories/Debt/DebtRepository.php` (interface) | 50 |
| `app/Repositories/Debt/EloquentDebt.php` | 99 |
| `app/Repositories/DebtHistory/DebtHistoryRepository.php` (interface) | 40 |
| `app/Repositories/DebtHistory/EloquentDebtHistory.php` | 79 |
| `app/Repositories/DebtProduct/DebtProductRepository.php` (interface) | 40 |
| `app/Repositories/DebtProduct/EloquentDebtProduct.php` | 74 |
| `resources/views/content/Printer/facteur-client.blade.php` | 172 |

Related, out-of-scope-but-touched artifacts read for context:
- `routes/web.php:1-105`
- `docs/audit/routes.json`
- `config/constant.php:1-19`
- `database/migrations/2024_09_19_164130_create_debts_table.php`
- `database/migrations/2024_09_19_164340_create_debt_products_table.php`
- `database/migrations/2024_10_24_192726_add_debt_paid_to_debts_table.php`
- `database/migrations/2025_08_18_144113_create_debt_histories_table.php`
- `app/Providers/EloquentRepositoryProvider.php:36-38`
- `app/Http/Controllers/dashboard/Analytics.php:15-17,47,55-57` (external consumer of `Debt` static methods)

No `resources/views/content/Debt/*.blade.php` files were read line-by-line (out of assigned scope: only `Printer` views were listed) but their `view()` names are cited from the controllers for the case-mismatch finding below (git-tracked path confirmed via `git ls-files`).

## Classes & Responsibilities

### `App\Http\Controllers\Debt\DebtController` (`app/Http/Controllers/Debt/DebtController.php:19`)
- Extends `Controller` (`app/Http/Controllers/Debt/DebtController.php:19`). No traits, no interfaces.
- Constructor-injected: `DebtRepository $debt`, `DebtHistoryRepository $debtHistory`, `DebtProductRepository $debtProduct`, `CategoryRepository $category`, `TractorDriverRepository $tractorDriver` (`app/Http/Controllers/Debt/DebtController.php:27-34`).
- Public methods:
  - `index()` (`:36`) — list unpaid "client" debts (`tractor_driver_id = 1`) via `debt->debtUnPaid()`, plus categories and normal-type suppliers.
  - `indexPaid()` (`:48`) — list paid client debts via `debt->debtPaid()`.
  - `create()` (`:65`) — empty stub.
  - `store(Request $request): RedirectResponse` (`:76`) — validates, opens a DB transaction, creates a `Debt`, loops parallel arrays from the request to create `DebtProduct` rows, sums line amounts into `total_debt_amount`/`rest_debt_amount`, commits.
  - `show($id)` (`:152`) — loads one debt, renders `content.Debt.view`.
  - `edit($id)` (`:164`) — loads debt + categories, renders `content.Debt.edit`.
  - `update(Request $request, $id): RedirectResponse` (`:181`) — validates, transactionally updates debt header fields, then loops parallel arrays to create-or-update `DebtProduct` rows (branch on `id == 0`), recomputes `rest_debt_amount = total - debt_paid`, updates debt totals.
  - `destroy($id)` (`:282`) — deletes a debt (soft delete via model trait), **no transaction opened** (see Smells).
  - `payDebt(Request $request, $id)` (`:296`) — records a payment: marks named `DebtProduct` rows `status = 1`, computes new `debt_paid`/`rest_debt_amount`/`status` via a 4-way if/elseif chain, writes a `DebtHistory` row.
  - `searchName(Request $request)` (`:371`) — AJAX search by `fullname LIKE`, queries `Debt` model directly (bypasses repository — see Smells), returns JSON.

### `App\Http\Controllers\Debt\DebtWithSupplierController` (`app/Http/Controllers/Debt/DebtWithSupplierController.php:18`)
- Extends `Controller`. Same five repository dependencies as `DebtController` (`:27-34`).
- Near-identical method set to `DebtController`, operating on the "supplier" side of the same `debts` table (`tractor_driver_id != 1`):
  - `index()` (`:36`) — `driverDebtUnPaid()`.
  - `indexPaid()` (`:49`) — `driverDebtPaid()`.
  - `create()` (`:67`) — empty stub.
  - `store()` (`:78`) — byte-for-byte duplicate logic of `DebtController::store` (see Smells: duplication).
  - `show($id)` (`:159`) — renders `content.DebtWithSupplier.view`.
  - `edit($id)` (`:172`) — renders `content.DebtWithSupplier.edit`.
  - `update()` (`:189`) — duplicate of `DebtController::update`, redirects to `debt-supplier.index`.
  - `destroy($id)` (`:287`) — deletes, but **redirects to `debt.index`** not `debt-supplier.index` (`:292`) — likely copy-paste bug.
  - `payDebt()` (`:300`) — duplicate of `DebtController::payDebt`; on the "exceeds amount" branch also redirects to `debt.index` instead of `debt-supplier.index` (`:343`).

### `App\Http\Controllers\Print\PrinterController` (`app/Http/Controllers/Print/PrinterController.php:9`)
- Extends `Controller`. Injects `DebtRepository $debt` (`:13-16`).
- `factuerClient($id, $fullname)` (`:17`) — loads a debt by `$id` and renders the printable invoice view. **`$fullname` route parameter is accepted but never used/validated against the debt** (`app/Http/Controllers/Print/PrinterController.php:17-22`) — dead parameter, and no ownership/authorization check tying the requesting user to that debt (IDOR risk, see Smells).

### `App\Models\Debt` (`app/Models/Debt.php:10`)
- `extends Model`; traits `HasFactory`, `SoftDeletes`, `SoftCascadeTrait` (`:12`).
- `$fillable`: `user_id, tractor_driver_id, fullname, phone, date_debut_debt, total_debt_amount, debt_paid, rest_debt_amount, date_end_debt, status, note` (`:14-26`).
- `$softCascade = ['getDebtProduct']` (`:28`) — cascades soft-deletes to `DebtProduct` rows via the SoftCascade package.
- No `$casts` defined — `total_debt_amount`, `debt_paid`, `rest_debt_amount` are `decimal(20,2)` in the DB (see migrations) but arrive in PHP as strings (Eloquent default), which is consistent with the string-concatenation-safe arithmetic used in controllers but is not explicit/defensive.
- Relations: `getDebtProduct()` → `hasMany(DebtProduct::class)` (`:30-33`); `tractorDriver()` → `belongsTo(TractorDriver::class, 'tractor_driver_id')` (`:35-38`); `debtHistories()` → `hasMany(DebtHistory::class)` (`:69-72`).
- Static aggregate helpers (not scopes): `getTotalDebt()` (`:40-43`), `getTotalPaidDebt()` (`:44-47`), `getTotalRestDebt()` (`:49-52`) — each a bare `static::sum(...)` with **no filtering**, i.e. sums across both "client" and "supplier" debts combined (`tractor_driver_id` is not filtered). Consumed by `app/Http/Controllers/dashboard/Analytics.php:15-17,55-57`.
- `getDebtTimeline()` (`:54-67`) — raw `selectRaw` grouped by year/month using `total_debt_amount`, `debt_paid`, `rest_debt_amount`; also unfiltered by `tractor_driver_id`. Consumed by `Analytics.php:47`.
- No accessors/mutators, no `$casts`, no scopes (`whereStatus`, `whereTractorDriverId` used in repositories are Eloquent dynamic `where` magic methods, not declared local scopes).

### `App\Models\DebtHistory` (`app/Models/DebtHistory.php:8`)
- `extends Model`; trait `HasFactory` only — **no `SoftDeletes`**, though migration creates a `deleted_at` column (`database/migrations/2025_08_18_144113_create_debt_histories_table.php:22`) — mismatch (see Smells).
- `$fillable`: `debt_id, amount, date` (`:12-16`).
- Relation: `debt()` → `belongsTo(Debt::class)` (`:18-21`).
- No `$casts` for `amount` (decimal(8,2)) or `date` (datetime).

### `App\Models\DebtProduct` (`app/Models/DebtProduct.php:10`)
- `extends Model`; traits `HasFactory`, `SoftDeletes`, `SoftCascadeTrait` (`:12`).
- `$fillable`: `debt_id, subcategory_id, name_category, quantity, amount, date_debt, status` (`:14-22`).
- Relations: `getDebt()` → `belongsTo(Debt::class)` (`:24-27`); `getSubcategory()` → `belongsTo(Subcategory::class, 'subcategory_id')` (`:29-32`).
- `quantity` is a `string` column in the DB (migration `2024_09_19_164340_create_debt_products_table.php:21`) despite representing a numeric quantity — no cast.
- `status` is declared as `enum('status', [1,0])` in the migration (`:24`) which Laravel/MySQL will coerce to the strings `'1'`/`'0'` — unusual/fragile enum declaration (should be `tinyInteger` or `boolean`, or a real string enum).

### Repositories (interface + Eloquent implementation pattern, all under `App\Repositories\*`)
- `DebtRepository` (`app/Repositories/Debt/DebtRepository.php`) / `EloquentDebt` (`app/Repositories/Debt/EloquentDebt.php`):
  - `all()`, `getSupplier()`, `debtPaid()`, `debtUnPaid()`, `driverDebtPaid()`, `driverDebtUnPaid()`, `find($id)`, `create(array)`, `update($id, array)`, `delete($id)`, `paginate($perPage, $search = null)`.
  - `debtPaid()`/`debtUnPaid()` hardcode `whereTractorDriverId(1)` (magic string/int literal `1` = "the walk-in client" sentinel) (`app/Repositories/Debt/EloquentDebt.php:33-40`); `driverDebtPaid()`/`driverDebtUnPaid()` hardcode `tractor_driver_id != 1` (`:25-32`) — the `1` sentinel is repeated 4× here plus again in `routes/web.php:96` and nowhere centralized as a constant.
  - `paginate()` (`:87-98`) does not actually paginate — calls `->get()` and returns a plain Collection, then calls `$result->appends(...)` which is not a Collection method (only `LengthAwarePaginator` has `appends()`); if `$search` is ever truthy this call will throw a `BadMethodCallException`. Method also appears unused by the two controllers in scope (they use `debtUnPaid`/`debtPaid`/`driverDebtUnPaid`/`driverDebtPaid` instead).
  - No relation eager-loading (`with(...)`) anywhere in the repository — see N+1 risks below.
- `DebtHistoryRepository` / `EloquentDebtHistory` (`app/Repositories/DebtHistory/*.php`): plain CRUD + same broken `paginate()` pattern (`app/Repositories/DebtHistory/EloquentDebtHistory.php:67-78`), unused by scope's controllers except `create()`.
- `DebtProductRepository` / `EloquentDebtProduct` (`app/Repositories/DebtProduct/*.php`): plain CRUD + same broken `paginate()` pattern (`app/Repositories/DebtProduct/EloquentDebtProduct.php:62-73`).
- Bound in `app/Providers/EloquentRepositoryProvider.php:36-38` (`DebtRepository::class → EloquentDebt::class`, etc.) — standard Laravel service-container binding, no scoping/caching decorators.

## Data Flow (entrypoint -> exit)

**Create a client debt:**
`POST /debt` → `DebtController::store` (`app/Http/Controllers/Debt/DebtController.php:76`) → `Validator::make` (fullname/phone/date_debut_debt only — **line items are not validated**) → `DB::beginTransaction()` → `DebtRepository->create()` → `EloquentDebt::create()` → `Debt::create()` (INSERT) → loop over `name_product[]`/`quantity[]`/`amount_due[]`/`date_debt[]`/`subcategory_ids[]` request arrays → `DebtProductRepository->create()` per row (INSERT) → `DebtRepository->update()` writes back computed `total_debt_amount`/`rest_debt_amount` (UPDATE) → `DB::commit()` → redirect back with toastr flash.

**Record a payment:**
`PATCH /debt/pays/{debt}` → `DebtController::payDebt` (`:296`) → `DB::beginTransaction()` → `DebtRepository->find()` (SELECT) → optionally flips selected `DebtProduct.status = 1` per id (N updates) → 4-branch if/elseif recomputing `debt_paid`/`rest_debt_amount`/`status`/`date_end_debt` → `DebtRepository->update()` (UPDATE) → `DebtHistoryRepository->create()` writes an audit row (INSERT) → `DB::commit()` → redirect back.

**Print invoice:**
`GET /print/printer-facteur/{debt}/{fullname}` → `PrinterController::factuerClient` (`:17`) → `DebtRepository->find($id)` (SELECT, no eager load) → view `content.Printer.facteur-client` iterates `$debt->getDebtProduct` (lazy-loads N products) and per-product `$item->getSubcategory->display_name` (lazy-loads 1 subcategory per product) → renders static HTML print page (client-side `html2canvas`/`jsPDF` for optional PDF download, dead code path — button is commented out at `resources/views/content/Printer/facteur-client.blade.php:123-125` while the `#invoice_download_btn` click handler at `:140-166` remains wired).

**Dashboard aggregate consumption (adjacent, not in scope but a real caller):**
`Debt::getTotalDebt()/getTotalPaidDebt()/getTotalRestDebt()/getDebtTimeline()` are called directly (static, bypassing the repository layer) from `app/Http/Controllers/dashboard/Analytics.php:15-17,47,55-57`, mixing client and supplier debts in every aggregate since none of these methods filter by `tractor_driver_id`.

**Unauthenticated read path (found while tracing routes, in scope as it renders debt data):**
`GET /list/debt/supplier` (`routes/web.php:92-99`) is a route Closure, registered **outside** the `middleware(['auth'])` group (compare `routes/web.php:51` group boundaries at `:82`, this route sits at `:92`), directly does `Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->get()` and renders `content.Liste.index`. Confirmed via `docs/audit/routes.json`: this route's `middleware` array is `["web"]` only — no `Authenticate` entry — unlike every other debt route which shows `["web","App\\Http\\Middleware\\Authenticate"]`.

## External Dependencies (packages, APIs, queues)

- `Askedio\SoftCascade\Traits\SoftCascadeTrait` — third-party package used by `Debt` (`app/Models/Debt.php:5,12,28`) and `DebtProduct` (`app/Models/DebtProduct.php:5,12`) to cascade soft-deletes to child rows.
- `toastr()` helper (flash-message package, e.g. `brian2694/laravel-toastr` or similar) — used throughout both controllers for success/error flashes.
- Front-end print assets loaded via `asset('print/assets/...')`: `bootstrap.min.css`, `style.css`, `html2canvas.js`, `jquery.min.js`, `jspdf.min.js` (`resources/views/content/Printer/facteur-client.blade.php:10-12,135-137`) — client-side PDF generation, no server-side PDF library involved.
- No queues, jobs, listeners, events, observers, FormRequest classes, or Policy classes were found anywhere referencing `Debt`, `DebtHistory`, `DebtProduct`, or `Printer` (searched `app/Http/Requests`, `app/Policies`, `app/Observers`, `app/Jobs`, `app/Listeners`, `app/Events` for "debt" — zero matches). All validation is inline `Validator::make()` in controllers; all authorization is implicit (route-level `auth` middleware only, no per-resource ownership check).
- `config/constant.php:4-7` defines `DEBTS_STATUS.PAID = 'paid'` / `DEBTS_STATUS.UNPAID = 'unpaid'`, used by both controllers instead of a real PHP enum.

## Smells & Debt

| file | line | issue | severity 1-5 |
|---|---|---|---|
| `app/Http/Controllers/Debt/DebtController.php` | 76-149, `app/Http/Controllers/Debt/DebtWithSupplierController.php:78-151` | `store()` logic (validation shape, transaction, product loop, total calc) is duplicated near-verbatim across the two controllers | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 181-274, `app/Http/Controllers/Debt/DebtWithSupplierController.php:189-279` | `update()` logic duplicated near-verbatim, including the create-vs-update-by-`id==0`-sentinel branching | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 296-369, `app/Http/Controllers/Debt/DebtWithSupplierController.php:300-366` | `payDebt()` 4-branch payment/status logic duplicated near-verbatim | 3 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 292 | `destroy()` redirects to `route('debt.index')` instead of `route('debt-supplier.index')` — copy-paste bug, sends supplier-debt users to the client-debt list | 3 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 343 | `payDebt()` "amount exceeds" branch redirects to `route('debt.index')` instead of `debt-supplier.index` — same copy-paste bug | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 284-293 | `destroy()` has no `DB::beginTransaction()`, yet the `catch` block calls `DB::rollBack()` — rollback with no active transaction throws `PDOException`/`RuntimeException` inside the catch, masking the real error | 4 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 287-298 | Same no-transaction-but-rollback-in-catch pattern in `destroy()` | 4 |
| `app/Http/Controllers/Debt/DebtController.php` | 270 | `dd($e->getMessage())` left in the `update()` catch block — halts execution and dumps internals to the response in production, dead-code-after-dd on the following `toastr()->error(...)` line | 5 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 275 | Same `dd($e->getMessage())` left in `update()` catch block | 5 |
| `routes/web.php` | 92-99 | `GET /list/debt/supplier` closure route sits outside the `auth` middleware group and queries `Debt` directly — unauthenticated public access to unpaid supplier-debt data (names, phone numbers, amounts) | 5 |
| `routes/web.php` | 92-98 | Route closure does `Debt::whereStatus(...)->where(...)->get()` directly — raw Model use bypassing the repository layer, duplicating `EloquentDebt::driverDebtUnPaid()` (`app/Repositories/Debt/EloquentDebt.php:29-32`) logic inline in the route file | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 371-382 | `searchName()` queries `Debt::where(...)` directly instead of going through `DebtRepository` — inconsistent with the rest of the controller | 2 |
| `app/Http/Controllers/Print/PrinterController.php` | 17-22 | No authorization/ownership check between the authenticated user and the requested `$id` — any authenticated user can view/print any debt's invoice by guessing the id (IDOR); `$fullname` route param is accepted but never validated against the debt, so it's decorative only | 4 |
| `app/Http/Controllers/Debt/DebtController.php` | 45 vs 155,171 | `index()`/`indexPaid()` render `view('content.debt.index')` / `content.debt.indexPaid')` (lowercase `debt`) while `show()`/`edit()` render `view('content.Debt.view')` / `content.Debt.edit')` (capital `Debt`). `git ls-files` confirms only `resources/views/content/Debt/*` (capital) is tracked — the lowercase-path views will 404 on a case-sensitive filesystem (Linux/most prod hosts), even though they resolve fine on Windows dev machines | 5 |
| `app/Repositories/Debt/EloquentDebt.php` | 87-98 | `paginate($perPage, $search)` ignores `$perPage`, calls `->get()` (a plain `Collection`) instead of `->paginate($perPage)`, then conditionally calls `->appends(...)` which does not exist on `Collection` — will throw if `$search` is truthy; method appears unused by in-scope controllers but is part of the public repository contract | 3 |
| `app/Repositories/DebtHistory/EloquentDebtHistory.php` | 67-78 | Same broken `paginate()` pattern (`get()` + non-existent `Collection::appends()`) | 2 |
| `app/Repositories/DebtProduct/EloquentDebtProduct.php` | 62-73 | Same broken `paginate()` pattern | 2 |
| `app/Http/Controllers/Print/PrinterController.php` | 19-21, `resources/views/content/Printer/facteur-client.blade.php:96-101` | N+1 risk: `DebtRepository::find()` does not eager-load `getDebtProduct`/`getSubcategory`; the view then loops `$debt->getDebtProduct` and accesses `$item->getSubcategory` per row — 1 + N + N queries for an invoice with N line items | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 154, `app/Http/Controllers/Debt/DebtWithSupplierController.php:161` | `show()` loads a bare `find($id)` with no eager loading before presumably rendering line items in `content.Debt.view` / `content.DebtWithSupplier.view` (views not in assigned scope, not read) — likely same N+1 pattern as the printer view | 2 |
| `app/Models/Debt.php` | 40-52 | `getTotalDebt()`, `getTotalPaidDebt()`, `getTotalRestDebt()` are unfiltered `sum()` calls that silently combine "client" (`tractor_driver_id = 1`) and "supplier" (`tractor_driver_id != 1`) debts into one number; consumers (`Analytics.php`) likely intend one or the other, or intend a combined total, but this is not documented/obvious from the method names | 3 |
| `app/Models/Debt.php` | 54-67 | `getDebtTimeline()` — same unfiltered mixing of client/supplier rows in a raw `selectRaw` aggregate | 3 |
| `app/Repositories/Debt/EloquentDebt.php` | 33-40 (+ `routes/web.php:96`) | The tractor-driver id `1` (meaning "walk-in client, no real driver") is a magic literal repeated in 4 repository methods plus once more in `routes/web.php`, with no named constant | 2 |
| `app/Models/DebtHistory.php` | 8-24 | Model has no `SoftDeletes` trait, but its migration (`database/migrations/2025_08_18_144113_create_debt_histories_table.php:22`) creates a `deleted_at` column via `$table->softDeletes()` — `DebtHistory::delete()` will hard-delete despite the schema supporting soft deletes | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 79-83, 89-149 | Only header fields (`fullname`, `phone`, `date_debut_debt`) are validated; the line-item arrays (`name_product`, `quantity`, `amount_due`, `date_debt`, `subcategory_ids`) are trusted from `$request->input(...)` with no `required`/`numeric`/array-length-match checks before being looped and summed into `$total` — a missing/mismatched array index throws an uncaught `ErrorException`/`TypeError` inside the try block (caught generically, but no field-level feedback to the user) | 3 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` | 81-90, 92-151 | Same missing line-item validation as above | 3 |
| `app/Http/Controllers/Debt/DebtController.php` | 220-252, `app/Http/Controllers/Debt/DebtWithSupplierController.php:228-258` | `update()` decides create-vs-update per product row via `$idOld == 0` (loose `==` comparison on a value straight from request input) rather than `is_null`/`empty`/strict check — fragile if the client ever sends `"0"` vs `0` vs missing key inconsistently | 2 |
| `app/Http/Controllers/Debt/DebtController.php` | 296-369, `app/Http/Controllers/Debt/DebtWithSupplierController.php:300-366` | `payDebt()`'s 4-branch if/elseif chain recomputing `debt_paid`/`status`/`rest_debt_amount` is business logic embedded directly in the controller (no service/domain class), duplicated between the two controllers — a single "Debt payment" domain method would remove both the duplication and the controller bloat | 3 |
| `app/Http/Controllers/Debt/DebtController.php` (383 total) | 1-383 | Controller is close to the 150-LOC "fat controller" threshold across its 9 public actions (383 LOC / 9 methods), carrying validation, transaction management, and payment-calculation business logic that would typically live in a FormRequest + service/action class | 2 |
| `app/Http/Controllers/Debt/DebtWithSupplierController.php` (367 total) | 1-367 | Same fat-controller concern as `DebtController`, compounded by being ~95% duplicate code of it | 2 |

## Open Questions

- `resources/views/content/Debt/*.blade.php`, `resources/views/content/DebtWithSupplier/*.blade.php`, and `resources/views/content/Liste/index.blade.php` were **not** in the assigned file/dir scope and were not read line-by-line; the N+1 and business-logic-in-Blade risk noted for `show()`/`edit()` routes is inferred from controller `compact()` calls and the sibling `Printer` view's pattern, not confirmed by direct inspection.
- Whether the case-mismatch (`content.debt.index` vs tracked `content/Debt/index.blade.php`) actually causes failures depends on the production OS/filesystem and whether Laravel's view cache was ever warmed on a case-insensitive machine and shipped — could not be verified without deployment/environment info.
- Could not determine, from files in scope, whether any client-side or server-side authorization check exists for `PrinterController::factuerClient` beyond route-level `auth` middleware (no Policy/Gate found repo-wide for `debt`).
- The intent behind `Debt::getTotalDebt()`/`getTotalPaidDebt()`/`getTotalRestDebt()`/`getDebtTimeline()` mixing client and supplier rows (bug vs. intentional combined KPI) could not be confirmed without product/business input — `app/Http/Controllers/dashboard/Analytics.php` is outside this scope and was only grep-read for call sites, not fully analyzed.
- `composer.lock` shows as modified in the working tree (per repo status) but was not inspected; unclear if it affects any package used here (e.g. `askedio/laravel-soft-cascade`, toastr package) — flagged only as an awareness note, not a code smell within this scope.
