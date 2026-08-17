# Module: Catalog

Two-level materials catalog (Category → SubCategory) that supplies the line-item source for Debt. Also home
to a phantom "Supplier" controller/repository stack that is entirely dead code — real supplier data lives in
`tractor_drivers` (see `MODULES/vehicle-fleet.md`, `DOMAIN-MODEL.md`).

## Files

| File | LOC | Role |
|---|---:|---|
| `app/Http/Controllers/Category/CategoryController.php` | 138 | Category CRUD, live |
| `app/Http/Controllers/Category/SubCategoryController.php` | 99 | Only `show()` implemented; live AJAX dropdown endpoint |
| `app/Http/Controllers/Subcategory/SubcategoryController.php` | 85 | Fully stubbed, orphaned — different namespace/casing from the one above |
| `app/Http/Controllers/Supplier/SupplierController.php` | 115 | Orphaned, unroutable — depends on a repository interface with no implementation |
| `app/Models/Category.php` | 25 | |
| `app/Models/SubCategory.php` | 35 | |
| `app/Repositories/Category/{CategoryRepository,EloquentCategory}.php` | 40 / 77 | |
| `app/Repositories/SubCategories/{SubCategoryRepository,EloquentSubCategory}.php` | 42 / 80 | |
| `database/seeders/SupplierSeeder.php` | 30 | Named "Supplier", seeds `TractorDriver` rows |

Not found anywhere under `app/` (confirmed by search): `app/Models/Supplier.php`,
`app/Repositories/Supplier/*`. There is no `resources/views/content/supplier/` directory either.

## Responsibilities

### `CategoryController` (`app/Http/Controllers/Category/CategoryController.php:12`)
Injects `CategoryRepository`, `SubCategoryRepository` (`:17-21`). `index()` (`:24-28`) loads both
`category->paginate(10)` and `subcategory->paginate(10)` — neither call actually paginates (see Repository
layer below). `store()`/`update()` (`:46-66`, `:97-118`) use inline `Validator::make` (name required, max 255)
then redirect to `route('building-materals.index')` (`:59`) — **this route name does not exist**; the actual
registered name is `services.building-materials` (`routes/web.php:53`, note also the URI-vs-name spelling
mismatch: `services/building-materals` vs `.building-materials`). Every successful create/update/delete throws
`RouteNotFoundException` on the redirect (`ARCHITECTURE.md` V16). `destroy()` (`:126-137`) has no
`DB::transaction()`, unlike the equivalent single-row operation in `SupplierController`.

### `SubCategoryController` (`app/Http/Controllers/Category/SubCategoryController.php:9`, capital "C")
All CRUD methods are empty stubs except `show(Request $request, $id)` (`:54-64`) — which **ignores the
route-bound `$id`** and reads `$id_sub = $request->id` instead, returning JSON. This is a cascading-dropdown
AJAX endpoint, consumed from `resources/views/content/Debt/index.blade.php:238` via a hardcoded dummy route
segment `'01'` plus the real id sent as query data — works only because the controller ignores the segment.

### `Subcategory\SubcategoryController` (`app/Http/Controllers/Subcategory/SubcategoryController.php:8`, lowercase "c")
Different namespace and class from the one above. Only `index()` is non-empty, and it renders a two-line
placeholder view (`<h1>Hello</h1>`, `resources/views/content/Subcategory/index.blade.php:9-10`). **Not
referenced by any route** — fully orphaned. Confusable with the live controller above by name alone.

### `Supplier\SupplierController` (`app/Http/Controllers/Supplier/SupplierController.php:11`)
Injects `App\Repositories\Supplier\SupplierRepository` (`:15-18`) — **this interface has no implementing
class anywhere in the codebase and no binding** in `EloquentRepositoryProvider` (`ARCHITECTURE.md` V14).
Unroutable: imported in `routes/web.php:9` but never registered on any route. If somehow constructed, it
would fail at container resolution. Redirect messages say "Delivery driver added successfully" — copy-pasted
text from the TractorDriver/Vehicle domain, further evidence this was never finished.

### `Category` model (`app/Models/Category.php:10`)
`$softCascade = ['getSubcategories']` (`:18`). Relation `getSubcategories()` → `hasMany(SubCategory)` (`:20-23`)
— named against Eloquent convention (`getX` instead of a bare relation name).

### `SubCategory` model (`app/Models/SubCategory.php:11`)
Relations: `getCategory()` → `belongsTo(Category, 'category_id')` (`:21-24`); `getDebtProducts()` →
`hasMany(DebtProduct)` (`:26-29`) — the edge into the Debt module. Accessor `getDisplayNameAttribute()`
(`:31-34`) maps rebar-size name values to the Arabic label `ريموك` — business/domain logic embedded as a
model accessor; see `GLOSSARY.md`.

## Repository layer

`EloquentCategory::paginate()` (`:65-76`) and `EloquentSubCategory::paginate()` (`:68-79`) share the codebase-
wide bug: they call `->get()` and ignore `$perPage`, returning a full `Collection` under a `paginate()` name
(`ARCHITECTURE.md` V10). The `if ($search) { $result->appends(...) }` branch in both is dead/broken —
`appends()` doesn't exist on `Collection` — currently unreachable since no caller passes `$search`.
`EloquentSubCategory::get($id)` (`:25-28`) — `SubCategory::where('category_id', $id)->get()` — backs the
`SubCategoryController::show` AJAX endpoint above. Neither method eager-loads `getCategory`, and
`resources/views/content/Category/index.blade.php:80` accesses `$subcategory->getCategory->name` inside a
`@foreach` — a classic N+1.

Binding: `app/Providers/EloquentRepositoryProvider.php:34-35` binds `CategoryRepository` and
`SubCategoryRepository`. No binding exists for any Supplier repository.

## Data flow

**Category CRUD:** `Route::resource` (`services/building-materals`) → `CategoryController` →
`CategoryRepository` → `Category` model → redirect (broken, see above) → `content.category.index`, which
independently loads all subcategories for a second table on the same page.

**Subcategory dropdown:** `services/subcategory` → resolves at runtime to `Category\SubcategoryController`
per the live route dump — note the case mismatch against the actual file/class `SubCategoryController`
(capital C). This resolves only because the current dev environment is on a case-insensitive filesystem;
PSR-4 autoloading is case-sensitive on typical Linux production filesystems and this would fatal there
(`ARCHITECTURE.md` §5.2).

**Supplier CRUD:** no entrypoint exists — dead branch.

**"Supplier" domain data in practice:** `database/seeders/SupplierSeeder.php` seeds two `TractorDriver` rows
(fullname `supplier`, `بن لادن`) — the actual runtime concept of "supplier" is a `TractorDriver` record, not
anything in this Supplier controller/repository stack.

## Known issues (severity 4-5)

| Issue | Where | Severity |
|---|---|---|
| Route-name case mismatch: routes import `SubcategoryController` (lowercase c), file declares `SubCategoryController` | `routes/web.php:5,54` vs `app/Http/Controllers/Category/SubCategoryController.php:9` | 5 |
| Two overlapping "subcategory" controllers under different namespaces, one live, one dead-stub | `Category/SubCategoryController.php` vs `Subcategory/SubcategoryController.php` | 4 |
| `SupplierController` depends on a nonexistent repository interface, unroutable dead code | `Supplier/SupplierController.php:6,15` | 4 |
| `paginate()` on Category/SubCategory ignores `$perPage`, returns unbounded `Collection` | `EloquentCategory.php:65-76`, `EloquentSubCategory.php:68-79` | 4 |
| N+1: `$subcategory->getCategory->name` with no eager-load | `Category/index.blade.php:80` | 3 |

Full list with line numbers: `docs/audit/raw/04-catalog.md`.

## Open questions

- Whether `Subcategory\SubcategoryController` is a work-in-progress replacement for `Category\SubCategoryController`
  that was never wired up, or safe to delete outright — not determinable from static code.
- Whether `Category/create.blade.php`/`edit.blade.php` partials are actually included anywhere — their
  `@include` lines are commented out in `index.blade.php:16,50`; needs runtime confirmation.
- Whether `Supplier\SupplierController` is a remnant of an abandoned real-Supplier-entity effort superseded
  by `TractorDriver` rows, or an unfinished feature still intended to be completed.
