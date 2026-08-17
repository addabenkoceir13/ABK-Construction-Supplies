# Scope: Catalog (Category/Subcategory/Supplier)

## Files

| File | LOC | Role |
|---|---:|---|
| app/Http/Controllers/Category/CategoryController.php | 138 | RESTful controller for "Building Materials" (Category resource) |
| app/Http/Controllers/Category/SubCategoryController.php | 99 | Controller (namespace `Category`, class `SubCategoryController`) — only `show()` implemented, wired into live route |
| app/Http/Controllers/Subcategory/SubcategoryController.php | 85 | Controller (namespace `Subcategory`, class `SubcategoryController`) — fully stubbed, orphaned (see Smells) |
| app/Http/Controllers/Supplier/SupplierController.php | 115 | Controller for a "Supplier" resource — orphaned, unroutable (see Smells) |
| app/Models/Category.php | 25 | Eloquent model, table `categories` |
| app/Models/SubCategory.php | 35 | Eloquent model, table `subcategories` |
| app/Repositories/Category/CategoryRepository.php | 40 | Repository interface for Category |
| app/Repositories/Category/EloquentCategory.php | 77 | Repository implementation for Category |
| app/Repositories/SubCategories/SubCategoryRepository.php | 42 | Repository interface for SubCategory |
| app/Repositories/SubCategories/EloquentSubCategory.php | 80 | Repository implementation for SubCategory |
| resources/views/content/Category/index.blade.php | 152 | Category + Subcategory listing table (two DataTables) |
| resources/views/content/Category/create.blade.php | 29 | "Add building materials" modal/form |
| resources/views/content/Category/edit.blade.php | 54 | "Edit/Delete" modals for a category |
| resources/views/content/Category/destroy.blade.php | 22 | Standalone delete-confirmation modal (appears duplicative of the delete modal in edit.blade.php) |
| resources/views/content/Subcategory/index.blade.php | 12 | Placeholder scaffold view (`<h1>Hello</h1>`), target of the orphaned `Subcategory\SubcategoryController@index` |
| database/migrations/2024_09_19_164030_create_categories_table.php | 33 | `categories` table schema |
| database/migrations/2024_09_19_164035_create_subcategories_table.php | 35 | `subcategories` table schema |
| database/seeders/SupplierSeeder.php | 30 | Seeder named "Supplier" but seeds `App\Models\TractorDriver` rows, not a Supplier model |

Not found in scope (searched, do not exist anywhere under `app/`):
- `app/Models/Supplier.php`
- `app/Repositories/Supplier/SupplierRepository.php`
- `app/Repositories/Supplier/EloquentSupplier.php` (or any file under `app/Repositories/Supplier/`)
- `resources/views/content/supplier/*` (no such directory exists; only `resources/views/content/DebtWithSupplier/*`, a separate, unrelated Debt-module feature)

## Classes & Responsibilities

### `App\Http\Controllers\Category\CategoryController` (app/Http/Controllers/Category/CategoryController.php:12)
- Extends `App\Http\Controllers\Controller`.
- No traits.
- Constructor-injected: `CategoryRepository $category`, `SubCategoryRepository $subcategory` (app/Http/Controllers/Category/CategoryController.php:17-21).
- `index()` (24-28): loads both `category->paginate(10)` and `subcategory->paginate(10)` and returns `content.category.index` — despite the name, neither repository call actually paginates (see Smells).
- `create()` (35-38): empty stub, return type `void` (inconsistent with resource-controller convention, no view returned — the "create" UI is a modal embedded directly in `index.blade.php` instead).
- `store(Request $request): RedirectResponse` (46-66): inline `Validator::make` (name required, max 255) → `category->create($request->all())` inside try/catch → toastr + redirect to `building-materals.index`.
- `show($id)` / `edit($id)` (74-88): empty stubs, `void` return type.
- `update(Request $request, $id): RedirectResponse` (97-118): same inline validation pattern → `category->update($id, $request->all())`.
- `destroy($id)` (126-137): `category->delete($id)` inside try/catch, no `DB::transaction`.

### `App\Http\Controllers\Category\SubCategoryController` (app/Http/Controllers/Category/SubCategoryController.php:9) — note class name `SubCategoryController` (capital C)
- Extends `Controller`. Constructor-injected: `SubCategoryRepository $subcategory` (13-16).
- `index/create/store/edit/update/destroy`: all empty stubs (no CRUD is actually implemented for subcategories through this controller).
- `show(Request $request, $id)` (54-64): the only real method. Ignores the route-bound `$id` parameter entirely and instead reads `$id_sub = $request->id` from the request payload/query string, calls `subcategory->get($id_sub)`, and returns JSON `{status, message, data}`. This is an AJAX endpoint used to populate a subcategory dropdown for a given category (confirmed consumer below).

### `App\Http\Controllers\Subcategory\SubcategoryController` (app/Http/Controllers/Subcategory/SubcategoryController.php:8) — different namespace, different class
- Extends `Controller`. No dependencies injected, no repository used at all.
- `index()` (15-18): `return view('content.Subcategory.index')` — the only non-empty method; the target view is a two-line placeholder (`resources/views/content/Subcategory/index.blade.php:9-10`, literally `<h1>Hello</h1>`).
- `create/store/show/edit/update/destroy`: all empty stubs.
- Not referenced by any route (see Smells — orphaned controller).

### `App\Http\Controllers\Supplier\SupplierController` (app/Http/Controllers/Supplier/SupplierController.php:11)
- Extends `Controller`. Constructor-injected: `App\Repositories\Supplier\SupplierRepository $supplier` (15-18) — **this interface has no implementing class anywhere in the codebase and no binding in `app/Providers/EloquentRepositoryProvider.php`** (verified against the full bind list at app/Providers/EloquentRepositoryProvider.php:34-42, which has no Supplier entry).
- `index()` (20-24): `supplier->all()` → `view('content.supplier.index', ...)` — that view directory does not exist under `resources/views/content/`.
- `store()` (31-56): validates `fullname`, `phone`; wraps `supplier->create()` in `DB::beginTransaction()/commit()/rollBack()`. Redirect messages say "Delivery driver added successfully" (copy-pasted text from the TractorDriver/Vehicle-driver domain, not "Supplier").
- `update()` (70-95) / `destroy()` (98-114): same transactional pattern, same "Delivery driver ..." toastr text.
- `create()` / `show()` / `edit()`: empty stubs.
- **Not referenced by any route** — imported in routes/web.php:9 but never used in a `Route::resource`/`Route::get` call (verified via full read of routes/web.php, 107 lines, and cross-checked against `docs/audit/routes.json`, which contains zero entries whose `action` references `Supplier\SupplierController`).

### `App\Models\Category` (app/Models/Category.php:10)
- Traits: `HasFactory`, `SoftDeletes`, `SoftCascadeTrait` (Askedio\SoftCascade).
- `$table = 'categories'`, `$fillable = ['name']`.
- `$softCascade = ['getSubcategories']` (app/Models/Category.php:18) — soft-cascades deletes to subcategories via the trait.
- Relation: `getSubcategories()` → `hasMany(SubCategory::class)` (20-23). Note: method is named `getSubcategories` rather than the Eloquent convention `subcategories`.

### `App\Models\SubCategory` (app/Models/SubCategory.php:11)
- Traits: `HasFactory`, `SoftDeletes`, `SoftCascadeTrait`.
- `$table = 'subcategories'`, `$fillable = ['category_id', 'name', 'input_type']`.
- Relations: `getCategory(): BelongsTo` → `belongsTo(Category::class, 'category_id')` (21-24); `getDebtProducts()` → `hasMany(DebtProduct::class)` (26-29) — links this scope to the Debt module.
- Accessor: `getDisplayNameAttribute()` (31-34) — business rule mapping name values `1/4, 2/4, 3/4, 4/4 = 1` to the Arabic label `ريموك` (rebar/steel-bar sizes, presumably); this is domain logic embedded as a model accessor.
- No `$casts` defined.

### `App\Repositories\Category\CategoryRepository` (interface, app/Repositories/Category/CategoryRepository.php:5) and `EloquentCategory` (app/Repositories/Category/EloquentCategory.php:10)
- Methods: `all()`, `find($id)`, `create(array)`, `update($id, array)`, `delete($id)`, `paginate($perPage, $search = null)`.
- `paginate()` (EloquentCategory.php:65-76) does **not** call Eloquent's `paginate()` — it runs `Category::query()->orderBy('id','desc')->get()` and returns the full `Collection`; the `$perPage` argument is silently ignored. The `if ($search) { $result->appends(...) }` branch is dead/broken: `appends()` is a `LengthAwarePaginator` method, not available on a plain `Collection` — calling it here would throw a `BadMethodCallException` if `$search` were ever non-null (currently unreachable since callers never pass `$search`).

### `App\Repositories\SubCategories\SubCategoryRepository` (interface, app/Repositories/SubCategories/SubCategoryRepository.php:5) and `EloquentSubCategory` (app/Repositories/SubCategories/EloquentSubCategory.php:8)
- Methods: `all()`, `find($id)`, `get($id)`, `create()`, `update()`, `delete()`, `paginate()`.
- `get($id)` (25-28): `SubCategory::where('category_id', $id)->get()` — used by `SubCategoryController@show` for the cascading-dropdown AJAX endpoint.
- `paginate()` (68-79): identical bug pattern to `EloquentCategory::paginate()` — ignores `$perPage`, calls `->get()` instead, and the `$search`/`appends()` branch is dead/broken for the same reason.
- Neither `get()` nor `paginate()` eager-loads the `getCategory` relation (see N+1 in Smells).

### Binding (app/Providers/EloquentRepositoryProvider.php:34-42)
```
CategoryRepository::class    -> EloquentCategory::class     (line 34)
SubCategoryRepository::class -> EloquentSubCategory::class  (line 35)
```
No binding exists for any `Supplier` repository interface — confirming `SupplierController`'s constructor dependency cannot be resolved from a purpose-built binding; if it resolved at all it would only be through Laravel's zero-config autowiring, which requires the class to exist, and it does not.

## Data Flow (entrypoint -> exit)

**Category CRUD** (`services/building-materals`, routes/web.php:53, named `services.building-materials.*`):
`Route::resource` → `App\Http\Controllers\Category\CategoryController` → `App\Repositories\Category\CategoryRepository` (bound to `EloquentCategory`) → `App\Models\Category` (table `categories`) → redirect back to `content.category.index` view, which also independently loads all subcategories for a second table on the same page (app/Http/Controllers/Category/CategoryController.php:25-27).

**Subcategory dropdown fetch** (`services/subcategory`, routes/web.php:54, named `services.subcategory.*`, resolves at runtime to controller `App\Http\Controllers\Category\SubcategoryController` per `docs/audit/routes.json` — see Smells for the case-mismatch with the actual file/class `SubCategoryController`):
Consumed from `resources/views/content/Debt/index.blade.php:238` via `$.ajax({ url: route('services.subcategory.show','01'), data: {id: id} })` (a dummy `'01'` route-segment placeholder plus the real id sent as query data) → `SubCategoryController@show` reads `$request->id` (ignores the route id) → `SubCategoryRepository::get($id)` → JSON `{status, message, data}` back to the AJAX caller, which populates dependent form fields. This pattern is also referenced from `resources/views/content/DebtWithSupplier/*.blade.php` (per grep hits) though not read in full for this audit.

**Supplier CRUD**: entrypoint does not exist — no route reaches `SupplierController`. Dead branch; if manually invoked (e.g. via `app(SupplierController::class)`) it would fail at container resolution because `App\Repositories\Supplier\SupplierRepository` has no class file to autoload.

**"Supplier" domain data in practice**: `database/seeders/SupplierSeeder.php` seeds two `App\Models\TractorDriver` rows (fullname `supplier`, `بن لادن`) into the `tractor_drivers` table — i.e., the actual runtime concept of "supplier" in this app is represented by `TractorDriver` records, not by the `Supplier` controller/repository stack, which is entirely vestigial.

## External Dependencies (packages, APIs, queues)
- `Askedio\SoftCascade\Traits\SoftCascadeTrait` (composer package `askedio/laravel-soft-cascade` or similar) — used by both `Category` and `SubCategory` models for cascading soft-deletes (app/Models/Category.php:5,12,18; app/Models/SubCategory.php:5,13).
- `toastr()` helper (flash-message package, e.g. `spatie`/`brian2694/laravel-toastr`) — used in every controller for user feedback (e.g. app/Http/Controllers/Category/CategoryController.php:53,58,62).
- Front-end: `DataTable` (jQuery DataTables) initialized in `resources/views/content/Category/index.blade.php:105,129` for both the category and subcategory tables (client-side rendering/search, not server-side pagination — consistent with `paginate()` actually returning a full collection rather than a paginator, see Smells).
- No queue, external API, or event/listener usage found within this scope.

## Smells & Debt

| File | Line | Issue | Severity |
|---|---|---|---|
| app/Http/Controllers/Category/SubCategoryController.php:9 vs app/Http/Controllers/Subcategory/SubcategoryController.php:8 | 9 / 8 | Two controllers implement overlapping "subcategory" concerns under different namespaces/directories (`Category\SubCategoryController` vs `Subcategory\SubcategoryController`), one live (wired to a route, has real logic), the other fully stubbed and orphaned. Naming only differs by the capitalization of "Category"/"category". Confusing duplication, high risk of future contributors editing the wrong file. | 4 |
| routes/web.php:5,54 vs app/Http/Controllers/Category/SubCategoryController.php:9 | 5, 54, 9 | Route file imports/uses class name `SubcategoryController` (lowercase "c") from namespace `App\Http\Controllers\Category`, but the actual file `SubCategoryController.php` declares `class SubCategoryController` (uppercase "C"). Confirmed at runtime via `docs/audit/routes.json`, whose `action` for `services/subcategory.*` literally reads `App\\Http\\Controllers\\Category\\SubcategoryController`. This only resolves because the current dev environment (`C:\Herd\...`, Windows/NTFS) is case-insensitive; PSR-4 autoloading is case-sensitive on typical Linux production/CI filesystems (ext4) and this would break with a "Class not found" fatal error there. | 5 |
| app/Http/Controllers/Supplier/SupplierController.php:6,15 | 6, 15 | Constructor depends on `App\Repositories\Supplier\SupplierRepository`, which has no interface/implementation file anywhere under `app/` and no binding in `app/Providers/EloquentRepositoryProvider.php`. The whole controller is unroutable dead code (see next row) so it never actually gets constructed, but it represents a broken/never-finished feature left in the tree. | 4 |
| routes/web.php:9 (no matching Route::resource/Route::get anywhere in the file) | 9 | `SupplierController` is imported but never registered on any route. Cross-checked against `docs/audit/routes.json` (zero matches for `Supplier\\SupplierController` in any `action`). Fully orphaned controller. | 3 |
| app/Http/Controllers/Supplier/SupplierController.php:23 | 23 | `index()` returns `view('content.supplier.index', ...)`; no such view directory exists (`resources/views/content/` has `DebtWithSupplier/` only, not `supplier/`). Would throw `ViewNotFoundException` if ever reached. | 3 |
| app/Http/Controllers/Subcategory/SubcategoryController.php:17 | 17 | `index()` returns `view('content.Subcategory.index')`, which exists but is an unmodified two-line scaffold stub (`<h1>Hello</h1>`, resources/views/content/Subcategory/index.blade.php:9-10) — confirms this whole controller/namespace is unfinished/abandoned. | 3 |
| app/Repositories/Category/EloquentCategory.php:65-76; app/Repositories/SubCategories/EloquentSubCategory.php:68-79 | 65-76 / 68-79 | Methods named `paginate($perPage, $search)` do not paginate: they call `->get()` and ignore `$perPage` entirely, returning a full `Collection`. Misleading name/contract; callers (`CategoryController::index`, app/Http/Controllers/Category/CategoryController.php:25-26) believe they are getting a bounded page of 10 records but get every row. On a large catalog this is an unbounded query / potential performance and memory issue. | 4 |
| app/Repositories/Category/EloquentCategory.php:72-74; app/Repositories/SubCategories/EloquentSubCategory.php:75-77 | 72-74 / 75-77 | Dead/broken branch: `if ($search) { $result->appends(['search'=>$search]); }` calls `appends()` on a plain `Collection`, which has no such method (it belongs to `LengthAwarePaginator`). Currently unreachable since no caller passes `$search`, but latent bug if that parameter is ever wired up. | 2 |
| resources/views/content/Category/index.blade.php:80 combined with app/Repositories/SubCategories/EloquentSubCategory.php:68-79 | 80 | Blade template accesses `$subcategory->getCategory->name` inside a `@foreach` over the full subcategories collection, but `EloquentSubCategory::paginate()` never eager-loads `getCategory` (`with('getCategory')` absent). Classic N+1: one query per row rendered. | 3 |
| app/Http/Controllers/Category/SubCategoryController.php:54-64 | 54-56 | `show(Request $request, $id)` ignores the route-bound `$id` and reads `$request->id` instead; confirmed the caller (resources/views/content/Debt/index.blade.php:238) passes a hardcoded dummy `'01'` as the route segment and the real id via AJAX `data`. Works, but abuses RESTful `show` semantics and is easy to misread/misuse; a plain `Route::get('subcategory/list', ...)` would be clearer than a faux resource-show endpoint. | 2 |
| app/Http/Controllers/Category/CategoryController.php:35-38,74-77,85-88 | 35-38, 74, 85 | `create()`, `show()`, `edit()` are typed `: void` and are empty — the "create" UI is instead a Bootstrap modal embedded directly in `index.blade.php`. Not wrong, but signature/return-type churn (`void` vs the `RedirectResponse` used by store/update) suggests inconsistent authorship/no consistent controller contract. | 1 |
| app/Http/Controllers/Category/CategoryController.php:56-64,108-117,126-136 | 56-64, 108-117, 128-136 | Single-model `create`/`update`/`delete` calls wrapped in try/catch but not in `DB::transaction()`, unlike `SupplierController` which uses `DB::beginTransaction()/commit()/rollBack()` for equivalent single-model operations (app/Http/Controllers/Supplier/SupplierController.php:43-54). Inconsistent transactional discipline across controllers in the same domain area; low risk here since each operation touches one row, but stylistically inconsistent. | 1 |
| resources/views/content/Category/edit.blade.php:51; resources/views/content/Category/destroy.blade.php:19 | 51 / 19 | Malformed closing tag: `<form>` used where `</form>` was clearly intended, at the end of the delete-confirmation modal markup. Invalid HTML (unclosed form persists, nesting risk for subsequent modal markup on the same page). | 2 |
| resources/views/content/Category/destroy.blade.php (whole file, 22 lines) vs resources/views/content/Category/edit.blade.php:33-54 | 1-22 / 33-54 | `destroy.blade.php` duplicates the exact delete-confirmation modal already present inside `edit.blade.php` (same modal id pattern `modalDeleteBuilding{{ $category->id }}` vs `modalDeleteBuilding-{{ $category->id }}` — note the differing hyphen, a second latent inconsistency). Neither file's `@include` was found referenced from `index.blade.php` other than the commented-out `{{-- @include('content.Category.create') --}}` / `{{-- @include('content.Category.edit') --}}` at index.blade.php:16,50 — meaning the create/edit/destroy partials may not even be included in the rendered page (unconfirmed, listed as Open Question). | 2 |
| app/Http/Controllers/Category/CategoryController.php (whole file) | — | Controller performs direct repository-mediated model access, not raw `DB::`/`Model::` calls, so no "direct Model use inside controller" violation; noted here only to confirm the pattern is otherwise clean of that particular smell. | — |

## Open Questions
- `resources/views/content/Category/index.blade.php:16,50` comment out `@include('content.Category.create')` / `@include('content.Category.edit')`. It is unclear from static reading whether these partials are pulled in some other way (e.g. a layout/section elsewhere) or whether the create/edit/destroy modals are simply dead markup never rendered. Needs runtime/browser confirmation — not verifiable by static read alone.
- Whether `App\Http\Controllers\Subcategory\SubcategoryController` and `resources/views/content/Subcategory/index.blade.php` are safe to delete outright, or whether they are a work-in-progress replacement for `Category\SubCategoryController` that was never finished/wired up. No commit history was consulted (out of scope for static analysis) to determine intent/recency.
- Whether `App\Http\Controllers\Supplier\SupplierController` and its missing `SupplierRepository` are remnants of an abandoned "real Supplier entity" effort that was superseded by using `TractorDriver` rows as pseudo-suppliers (per `SupplierSeeder`), or an unfinished feature still intended to be completed. Cannot be determined from static code alone.
- Full contents of `resources/views/content/DebtWithSupplier/*.blade.php` were not read in this audit (out of the declared Catalog scope); they were only grepped for references to `services.subcategory`. If a fuller Debt-module audit is done separately, cross-check those files' use of the subcategory AJAX endpoint noted above.
- Could not find any `FormRequest`, `Policy`, `Job`, `Listener`, `Observer`, or model `$casts` tied to `Category`/`SubCategory`/Supplier — confirmed absent by grep/glob, not merely unread.
