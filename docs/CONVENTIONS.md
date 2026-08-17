# CONVENTIONS — ABK Construction Supplies

The de-facto rules found in this codebase, not an aspirational style guide. Each row states what the code
actually does most of the time, and whether that's safe to imitate. Consistency scores and full evidence are
in `ARCHITECTURE.md` §5 — this document is the "if you're adding code today, do this / don't do that" version.

## Controllers

| Rule (as practiced) | Follow it? | Evidence |
|---|---|---|
| One controller per resource under `app/Http/Controllers/<Module>/<Resource>Controller.php`, `Route::resource`-shaped methods (`index/create/store/show/edit/update/destroy`) even when `create`/`show`/`edit` are empty stubs. | Yes — it's the load-bearing shape every live controller uses. | e.g. `app/Http/Controllers/Category/CategoryController.php` |
| Constructor-inject one repository interface per related model (`DebtRepository $debt`, ...). | Yes, for new resources. | `app/Http/Controllers/Debt/DebtController.php:27-34` |
| Reach past the injected repository into `Model::` statics when convenient. | **No.** Four handlers do this and it's flagged as a violation (V4) in every audit pass — it defeats the one abstraction the codebase has. | `app/Http/Controllers/dashboard/Analytics.php:15-28` |
| Validate with `Validator::make(...)->fails()` inline in the controller method. | Yes, it's the dominant dialect (used in ~80% of write endpoints) — but see "Validation" below for the gap this leaves. | `app/Http/Controllers/Category/CategoryController.php:48-55` |
| Wrap multi-step writes in `DB::beginTransaction()/commit()/rollBack()`. | Yes, when the operation touches more than one table (`Debt` + `DebtProduct`, `Vehicle` + `InsuranceVehicle`). **Do not** call `rollBack()` in a `destroy()` unless you also call `beginTransaction()` — three `destroy()` methods currently crash their own catch block this way. | `app/Http/Controllers/Debt/DebtController.php:284-293` (broken example — do not copy) |
| Leave `dd()`/commented `dd()` calls in controller bodies. | **No** — two are live in production catch blocks today (`DebtController.php:270`, `DebtWithSupplierController.php:275`) and halt the response. Don't add more, and remove these when you touch either file. | — |

There is no `app/Http/Requests` (FormRequest) anywhere. If you add validation, either match the existing
inline `Validator::make()` dialect for consistency, or introduce FormRequests deliberately as a
module-by-module migration — don't mix `$request->validate()` and `Validator::make()` in the same controller.

## Repositories

| Rule (as practiced) | Follow it? | Evidence |
|---|---|---|
| One interface + one `Eloquent*` implementation per model, under `app/Repositories/<Model>/`. | Yes. | 9 pairs, all bound in `app/Providers/EloquentRepositoryProvider.php:34-42` |
| Bind every new pair in `EloquentRepositoryProvider::register()`. | Yes — it's the **only** file in the app that calls `bind()`. Forgetting this step means `BindingResolutionException` at container time for any controller that type-hints the interface. | `app/Providers/EloquentRepositoryProvider.php:32-43` |
| Name a method `paginate($perPage, $search)` and have it call `->paginate($perPage)`. | **Only `EloquentFuelStation` does this correctly.** 6 of 7 other `paginate()` methods call `->get()` and return a full `Collection` under a paginator-shaped docblock, then conditionally call `->appends()` (a `LengthAwarePaginator`-only method) which throws if ever reached. **If you write a new `paginate()`, look at `app/Repositories/FuelStation/EloquentFuelStation.php:95,113,126` as the reference implementation, not any of the others.** | `ARCHITECTURE.md` V10 |
| Put domain rules (not just CRUD) in the repository. | Rare — the only real rule anywhere in this layer is the `tractor_driver_id = 1` client/supplier split in `EloquentDebt`. If you're tempted to put a business rule in a repository method, that's consistent with the one precedent that exists, but consider whether a service class would be clearer (none exist yet, so there's no established alternative). | `app/Repositories/Debt/EloquentDebt.php:22,27,31,35,39` |

## Naming

| Rule (as practiced) | Follow it? |
|---|---|
| PascalCase class names matching the filename, one class per file. | Yes, generally — with two confirmed exceptions to *not* repeat: `FulstationController` (missing "e") and the `SubCategoryController`/`SubcategoryController` capitalization collision across two namespaces. Don't create a third near-homonym class. |
| Relation methods named `getX()` instead of Eloquent's bare-noun convention (`getDebtProduct()` instead of `debtProduct()`). | This is the majority style in this codebase (`Debt::getDebtProduct`, `SubCategory::getCategory`, `Category::getSubcategories`). Match it for consistency with existing models rather than introducing bare-noun relations that would look inconsistent next to them — but be aware it's non-idiomatic Eloquent, so don't expect magic relation-name conventions (e.g. `with('debtProduct')`) to work without checking the actual method name first. |
| Route **name** and route **URI** spelled the same. | **No — actively violated.** `services/building-materals` (URI, typo) vs `services.building-materials` (name, correct spelling) is the clearest example; `redirect()->route('building-materals.index')` in `CategoryController.php:59` uses neither and throws. When adding a route, pick one spelling and use it in the URI, the `->name()` call, and every `route()`/`redirect()->route()` call — grep for the string before trusting it exists. |
| Magic literals for domain concepts instead of named constants. | **No — avoid.** `tractor_driver_id = 1` for "walk-in client" appears as a bare `1` in 6 places with no constant, and the string/int form is even inconsistent between call sites (`'1'` vs `1`). `config/constant.php` already holds `DEBTS_STATUS`/`TRACTORDRIVER_STATUS`/`TRACTORDRIVER_TYPE` — if you need a new sentinel, put it there instead of inlining it. |

## Views

| Rule (as practiced) | Follow it? |
|---|---|
| View directories are named to match the Eloquent model / controller, in the **same case** used elsewhere for that resource (e.g. tracked directory `Debt/`, not `debt/`). | The *directory names on disk* are consistently PascalCase-per-resource. The *`view()` calls in controllers* are not — several call sites use lowercase (`content.debt.index`, `content.fuelstation.index`, `content.category.index`) against PascalCase tracked directories (`Debt/`, `Fuelstation/`, `Category/`). This works only because the current dev machine's filesystem is case-insensitive. **Always match the tracked directory's exact case in `view()` calls** — this is the single highest-severity convention violation in the codebase (`ARCHITECTURE.md` §5.2) and will 500 in production on a case-sensitive filesystem. |
| One `index` / `create` (modal) / `edit` (modal) / `delete` (modal) file per resource, `@include`-d per-row inside the index's `@foreach`. | Yes, this is the consistent shape across Debt, DebtWithSupplier, Fuelstation, TractorDriver, Vehicle, Category. Follow it for new resources. |
| Compute totals/percentages/domain rules inline in Blade with `@php`. | **Avoid** — it's done in several places (`$total +=` in `Fuelstation/index.blade.php:12-17`, unguarded percentage division in both dashboard views, `explode(' - ', $vehicle->license_plate)` in `Vehicle/edit.blade.php:34-42`) and every instance is flagged as a smell. Compute in the controller or a model accessor instead. |

## Transactions & error handling

| Rule (as practiced) | Follow it? |
|---|---|
| `try { ... DB::beginTransaction() ... DB::commit(); } catch (\Exception $e) { DB::rollBack(); toastr()->error(...); return redirect()->back(); }` | Yes, this is the standard shape for any multi-row write. **Only call `rollBack()` inside a `catch` if `beginTransaction()` was actually called earlier in the same method** — three `destroy()` methods currently violate this and mask their real errors. |
| Report exceptions somewhere operators can see (logging service, alerting). | **No — there is none.** `Handler::register()` registers an empty `reportable()`. Every error becomes a user-facing toast and nothing else. If you're debugging a production issue, there is no server-side trail beyond the default Laravel log; don't assume alerting exists. |

## Data & money

| Rule (as practiced) | Follow it? |
|---|---|
| Declare `$casts` on models for money/date columns. | **No — only `User::email_verified_at` has any cast in the entire codebase.** `total_debt_amount`, `debt_paid`, `rest_debt_amount`, `liter`, `amount` are all `decimal(20,2)` in the DB but flow through PHP as strings. Arithmetic on them relies on PHP's implicit numeric-string coercion. If you add a new money column, match the existing (implicit) pattern for consistency, but be aware this is a real footgun — test arithmetic paths explicitly. |
| Keep decimal precision consistent across sibling money columns. | **No** — `debt_histories.amount` is `decimal(8,2)` while every other money column in the schema is `decimal(20,2)`, capping history amounts at 999,999.99. Match `decimal(20,2)` for any new money column, not the `debt_histories` outlier. |
| Pair `softDeletes()` migrations with `SoftDeletes` **and** `SoftCascadeTrait` on the model, plus a `$softCascade` array naming the child relations. | Mostly — `Vehicle`, `Category`, `Debt`, `DebtProduct` all declare `$softCascade`. `TractorDriver`, `FuelStation`, and `User` use the trait but declare **no** `$softCascade` property (harmless no-op today, but inconsistent — if you add a new child relation that should cascade, declare it explicitly rather than assuming the trait infers anything). `DebtHistory` is the one model whose migration adds `deleted_at` but whose class **omits `SoftDeletes` entirely** — `delete()` hard-deletes there. Don't repeat that mismatch: if the migration has `softDeletes()`, the model needs the trait. |

## What "matching the codebase" does *not* mean

Several patterns above are described as "the majority style" without being endorsed — e.g. inline Blade
math, magic literals, and the lowercase `view()` calls. Matching them keeps a single file internally
consistent with its neighbors, but every one of them is a documented defect (`ARCHITECTURE.md` §2, §5). When
in doubt: match the *shape* (controller → repository → model, transaction wrapping, toastr flashes) and
prefer the *safer* variant where the codebase itself is inconsistent (e.g. `EloquentFuelStation::paginate()`
over any of its six broken siblings; PascalCase `view()` calls over the lowercase ones).
