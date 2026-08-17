# Scope: Frontend/Views

## Files

Scope = `resources/views/` (all files except `resources/views/content/Printer/`, covered by another scope), `resources/lang/`, `public/` (top-level listing only), `webpack.mix.js`, `package.json`.

| File | LOC |
|---|---|
| `resources/views/_partials/macros.blade.php` | 32 |
| `resources/views/content/Category/create.blade.php` | 29 |
| `resources/views/content/Category/destroy.blade.php` | 22 |
| `resources/views/content/Category/edit.blade.php` | 54 |
| `resources/views/content/Category/index.blade.php` | 152 |
| `resources/views/content/Debt/create.blade.php` | 118 |
| `resources/views/content/Debt/delete.blade.php` | 23 |
| `resources/views/content/Debt/edit.blade.php` | 292 |
| `resources/views/content/Debt/index.blade.php` | 343 |
| `resources/views/content/Debt/indexPaid.blade.php` | 380 |
| `resources/views/content/Debt/payDebt.blade.php` | 189 |
| `resources/views/content/Debt/test.blade.php` | 176 |
| `resources/views/content/Debt/view.blade.php` | 129 |
| `resources/views/content/DebtWithSupplier/create.blade.php` | 130 |
| `resources/views/content/DebtWithSupplier/delete.blade.php` | 23 |
| `resources/views/content/DebtWithSupplier/edit.blade.php` | 292 |
| `resources/views/content/DebtWithSupplier/index.blade.php` | 322 |
| `resources/views/content/DebtWithSupplier/indexPaid.blade.php` | 317 |
| `resources/views/content/DebtWithSupplier/payDebt.blade.php` | 189 |
| `resources/views/content/DebtWithSupplier/view.blade.php` | 135 |
| `resources/views/content/Fuelstation/add.blade.php` | 92 |
| `resources/views/content/Fuelstation/delete.blade.php` | 23 |
| `resources/views/content/Fuelstation/edit.blade.php` | 90 |
| `resources/views/content/Fuelstation/index.blade.php` | 229 |
| `resources/views/content/Fuelstation/pagination-data.blade.php` | 89 |
| `resources/views/content/Fuelstation/paided.blade.php` | 32 |
| `resources/views/content/Fuelstation/test.blade.php` | 286 |
| `resources/views/content/Liste/index.blade.php` | 63 |
| `resources/views/content/Subcategory/index.blade.php` | 12 |
| `resources/views/content/TractorDriver/create.blade.php` | 54 |
| `resources/views/content/TractorDriver/deleted.blade.php` | 23 |
| `resources/views/content/TractorDriver/edit.blade.php` | 55 |
| `resources/views/content/TractorDriver/index.blade.php` | 138 |
| `resources/views/content/Vehicle/added-date.blade.php` | 92 |
| `resources/views/content/Vehicle/create.blade.php` | 93 |
| `resources/views/content/Vehicle/delete.blade.php` | 23 |
| `resources/views/content/Vehicle/edit.blade.php` | 124 |
| `resources/views/content/Vehicle/index.blade.php` | 138 |
| `resources/views/content/authentications/auth-forgot-password-basic.blade.php` | 47 |
| `resources/views/content/authentications/auth-login-basic.blade.php` | 73 |
| `resources/views/content/authentications/auth-register-basic.blade.php` | 75 |
| `resources/views/content/dashboard/dashboards-analytics.blade.php` | 461 |
| `resources/views/content/dashboard/index.blade.php` | 465 |
| `resources/views/content/dashboard/index2.blade.php` | 373 |
| `resources/views/content/dashboard/temp.blade.php` | 48 |
| `resources/views/content/pages/pages-account-settings-account.blade.php` | 184 |
| `resources/views/content/pages/pages-account-settings-connections.blade.php` | 201 |
| `resources/views/content/pages/pages-account-settings-notifications.blade.php` | 131 |
| `resources/views/content/pages/pages-misc-error.blade.php` | 24 |
| `resources/views/content/pages/pages-misc-under-maintenance.blade.php` | 25 |
| `resources/views/layouts/blankLayout.blade.php` | 9 |
| `resources/views/layouts/commonMaster.blade.php` | 40 |
| `resources/views/layouts/contentNavbarLayout.blade.php` | 81 |
| `resources/views/layouts/sections/footer/footer.blade.php` | 17 |
| `resources/views/layouts/sections/menu/submenu.blade.php` | 45 |
| `resources/views/layouts/sections/menu/verticalMenu.blade.php` | 220 |
| `resources/views/layouts/sections/navbar/navbar.blade.php` | 169 |
| `resources/views/layouts/sections/scripts.blade.php` | 33 |
| `resources/views/layouts/sections/scriptsIncludes.blade.php` | 23 |
| `resources/views/layouts/sections/styles.blade.php` | 48 |
| `resources/views/vendor/pagination/*.blade.php` (9 files: `bootstrap-4/5`, `custom`, `default`, `semantic-ui`, `simple-bootstrap-4/5`, `simple-default`, `simple-tailwind`, `tailwind`) | 19–106 each |

All files listed above are git-tracked (confirmed via `Glob`). `resources/views/content/Printer/facteur-client.blade.php` (171 LOC) exists but is out of scope per instructions.

`resources/lang/` **does not exist**. A root-level `lang/` directory exists (Laravel ≥9 convention) but is outside the assigned scope (`resources/lang/`) and was not inspected.

`public/` top-level (listing only, per instructions): `assets/`, `favicon.ico`, `index.php`, `mix-manifest.json`, `print/`, `robots.txt`, `vendor/`.

`webpack.mix.js` (root): Laravel Mix 6 config. Compiles SCSS/JS for `resources/assets/vendor/**` (core theme + Bootstrap/Popper/Shepherd via Babel), `resources/assets/js/**` and `resources/assets/css/**` (app assets), copies `boxicons` fonts, calls `mix.version()` (cache-busting, consumed via `mix()` in `styles.blade.php`/`scriptsIncludes.blade.php`), and wires `mix.browserSync('http://127.0.0.1:8000/')`.

`package.json` (root): `laravel-mix` 6, `bootstrap` 5.1, `jquery` 3.5, `boxicons`, `apexcharts-clevision`, `perfect-scrollbar`, `masonry-layout`, `highlight.js`; scripts `dev`/`watch`/`hot`/`prod` all delegate to `mix`. DataTables and SweetAlert2 are loaded as static assets from `public/assets/DataTables` / `public/assets/sweetalert2` (not npm packages — see External Dependencies).

## Classes & Responsibilities

Not applicable in the OOP sense — this scope is Blade templates, not PHP classes. Inventoried instead as templates grouped by role:

- **Layouts** (`layouts/`): `commonMaster.blade.php` is the HTML skeleton (`<head>`, `@yield('layoutContent')`, includes `styles`/`scriptsIncludes`/`scripts`). `blankLayout.blade.php` (auth/error pages) and `contentNavbarLayout.blade.php` (app pages, menu+navbar+footer) both `@extends('layouts/commonMaster')`. `contentNavbarLayout.blade.php:3-20` uses an `@php` block to default 7 layout-control variables (`$contentNavbar`, `$containerNav`, `$isNavbar`, `$isMenu`, `$isFlex`, `$isFooter`, `$customizerHidden`, `$pricingModal`) via `??` — this is view-configuration logic, acceptable for a layout template.
- **Partials** (`_partials/macros.blade.php`, `layouts/sections/*`): brand SVG, footer, navbar (language/theme switcher + user dropdown, hardcoded to `auth()->user()->name`), vertical menu (hardcoded nav tree, no DB-driven menu — the dead `$menuData[0]->menu` foreach version is commented out at `verticalMenu.blade.php:149-220`), submenu partial (recursive `@include`).
- **Content pages** (`content/*`): one directory per domain entity (`Category`, `Debt`, `DebtWithSupplier`, `Fuelstation`, `TractorDriver`, `Vehicle`, `Subcategory`, `Liste`, `authentications`, `dashboard`, `pages`), each following an `index` (+ optional `indexPaid`) / `create` (modal partial) / `edit` (modal partial) / `delete` (modal partial) split, `@include`-d into the index inside the row `@foreach`.
- **Vendor** (`vendor/pagination/*`): Laravel's stock pagination view set, unmodified except `custom.blade.php` which is a hand-written Bootstrap-5-icon-styled paginator, used explicitly via `->links('vendor.pagination.custom')` in `content/Fuelstation/index.blade.php:86`.

### Controller → view map (cross-referenced against `docs/audit/routes.json` and controller `view()` calls)

| Controller | Views rendered |
|---|---|
| `App\Http\Controllers\Debt\DebtController` | `content.debt.index` (line 45), `content.debt.indexPaid` (57), `content.Debt.view` (155), `content.Debt.edit` (171) |
| `App\Http\Controllers\Debt\DebtWithSupplierController` | `content.DebtWithSupplier.index` (46), `.indexPaid` (59), `.view` (163), `.edit` (179) |
| `App\Http\Controllers\FuelStation\FulstationController` | `content.Fuelstation.pagination-data` (38, AJAX partial), `content.fuelstation.index` (44, 97), `content.Fuelstation.index` (104), `content.Fuelstation.partials.actions` (88, AJAX partial — **file does not exist**, see Smells) |
| `App\Http\Controllers\Category\CategoryController` | `content.category.index` (27) |
| `App\Http\Controllers\TractorDriver\TractorDriverController` | `content.TractorDriver.index` (24) |
| `App\Http\Controllers\Vehicle\VehicleController` | `content.Vehicle.index` (27) |
| `App\Http\Controllers\dashboard\Analytics` | `content.dashboard.index` (51, route `/`), `content.dashboard.index2` (118, route `/template`) — `content.dashboard.dashboards-analytics` is referenced only in commented-out lines (50, 117) |
| `App\Http\Controllers\authentications\LoginBasic` / `RegisterBasic` / `ForgotPasswordBasic` | `content.authentications.auth-login-basic` / `auth-register-basic` / `auth-forgot-password-basic` |
| `App\Http\Controllers\TLDRController` (not in any scanned scope, found while tracing `content.dashboard.temp`) | `content.dashboard.temp` — form posts to `url('/tldr/action')`, which does **not** appear anywhere in `docs/audit/routes.json` |
| Route closure `GET /list/debt/supplier` (`routes/web.php`, outside `auth` middleware, documented in `docs/audit/raw/01-debt-billing.md`) | `content.Liste.index` |
| No controller found | `content.Category.destroy` (only ever `@include`-d, never `view()`-rendered directly — it's a modal partial), `content/pages/*` (5 files, 6 route/URL references inside them like `pages/account-settings-notifications`, none of which exist in `docs/audit/routes.json`), `content/Debt/test.blade.php`, `content/Fuelstation/test.blade.php` |

`resources/views/vendor/pagination/*` are invoked implicitly by Laravel's paginator (`$collection->links()`) wherever a `LengthAwarePaginator` is rendered; only `custom.blade.php` is explicitly named in the views inspected.

## Data Flow (entrypoint -> exit)

**Typical CRUD page** (e.g. Debt, Vehicle, Fuelstation, TractorDriver, Category): Controller `index()` queries via repository/model → `view('content.X.index', compact(...))` → index view `@extends('layouts/contentNavbarLayout')` → `@include`s `create` (add modal), then in a `@foreach` row loop `@include`s `edit`/`delete`(/`payDebt`/`paided`/`deleted`/`added-date`) modal partials once **per row** → each modal partial posts its own `<form>` to a named route (`store`/`update`/`destroy`/custom actions) → page-script `<script>` block (usually inline, not a separate JS asset) wires DataTables, SweetAlert2 confirmations, and dynamic add/remove-row UI via jQuery + inline AJAX calls back to routes like `services.subcategory.show` and `debt.search`.

**Fuel station list, specifically**: `content/Fuelstation/index.blade.php` renders a filter form + an `#content` div that is server-side-included with `pagination-data.blade.php` (table body only) on first load, then re-fetched via AJAX (`fetchContent()`, lines 125-155) hitting `fuel-stations.index` again and replacing `#content`/`#pagination`/`.total-amount` from the JSON response — a partial/AJAX pattern duplicated with the "select-all + bulk paid" flow (lines 164-227) that posts to `fuel-stations.update.status`.

**Print flow, Debt/DebtWithSupplier row action**: `content.Debt.index`/`content.DebtWithSupplier.index` link to `debt.printer-facteur-client` (out-of-scope `Printer` view, already documented in `docs/audit/raw/01-debt-billing.md`).

**Dashboard**: `Analytics@index` → `content.dashboard.index` (465 LOC) and `Analytics@index2` (route `/template`) → `content.dashboard.index2` (373 LOC) both receive the same shape of aggregate totals (`$TotalDebt`, `$TotalPaidDebt`, `$TotalRestDebt`, `$TotalFuel`, `$TotalLiter`, per-fuel-type totals, `$debtTimeline`, fuel timeline data) and independently re-derive percentages (`$TotalPaidDebt/$TotalDebt`, etc.) and re-render nearly the same ApexCharts widgets — see Smells (duplication, division-by-zero, business logic in Blade).

## External Dependencies (packages, APIs, queues)

- **jQuery** (bundled `assets/js/jquery-3.6.0.min.js`, also loaded a second time from CDN in `content/Debt/test.blade.php:123`) — used pervasively for AJAX, DataTables init, and manual DOM manipulation embedded directly in every `@section('page-script')` block.
- **DataTables** (`assets/DataTables/*` — static files, not the npm `datatables.net` package listed in `webpack.mix.js` externals) — initialized per-page with copy-pasted `initComplete` footer-search boilerplate (see Smells: duplication).
- **SweetAlert2** (`assets/sweetalert2/min.js`) — used for bulk-action confirmation in `Fuelstation/index.blade.php`.
- **ApexCharts** (`apex-charts` vendor lib) — used in both dashboard views for donut/pie/radial/line/area charts, fed server-computed aggregates via `@json(...)` and raw Blade interpolation.
- **html2canvas / jsPDF** — referenced only in the out-of-scope `Printer` view (already documented in scope 01).
- **Toastr-style flashes** — consumed but not rendered from this scope's views directly (controllers call `toastr()->...`; the actual toastr container/script lives in the shared layout's vendor scripts, not inspected further here as it's not present in the files read).
- No queues, jobs, listeners, WebSockets, or external HTTP API calls originate from any file in this scope. All AJAX calls target same-app named routes.

## Smells & Debt

| file | line | issue | severity 1-5 |
|---|---|---|---|
| `resources/views/content/Category/create.blade.php` | 9 | `route('building-materals.store')` — this named route does not exist (actual name is `services.building-materials.store`, confirmed against `docs/audit/routes.json`); submitting the "Add building materials" form throws `RouteNotFoundException` | 5 |
| `resources/views/content/Category/edit.blade.php` | 10, 41 | `route('building-materals.update', ...)` and `route('building-materals.destroy', ...)` — same non-existent route names; edit/delete modals are broken | 5 |
| `resources/views/content/Category/destroy.blade.php` | 9 | `route('building-materals.destroy', ...)` — same non-existent route name (this file appears to be dead/superseded by the inline delete modal in `edit.blade.php`, since no controller or view ever `@include`s it — see Open Questions) | 4 |
| `app/Http/Controllers/FuelStation/FulstationController.php` | 88 | `view('content.Fuelstation.partials.actions', ...)` — no file exists at `resources/views/content/Fuelstation/partials/actions.blade.php` (confirmed via repo-wide glob); any code path hitting this line throws `InvalidArgumentException: View [content.Fuelstation.partials.actions] not found` | 5 |
| `resources/views/content/Liste/index.blade.php` | 1 | Stray literal `h` character before `<!DOCTYPE html>` — renders as visible text at the top of the page body | 3 |
| `resources/views/content/Liste/index.blade.php` | 36-40 | Per-item `<tr>` opened inside the `@foreach` is never closed with `</tr>` before the totals `<tr>` at line 41 — malformed table markup on every render | 3 |
| `resources/views/content/Category/edit.blade.php` | 51 | `<form>` closing tag written as `<form>` instead of `</form>` — malformed markup (also present in `destroy.blade.php:19`) | 2 |
| `resources/views/content/Vehicle/index.blade.php` | 58-63 | `<a href="javascript:void(0);" ...>` (line 59) wraps a `<span>` (line 60) that is never closed with `</span>` before `</a>` (line 62) — malformed markup, only rendered when `$vehicle->insuranceDateExpiredLast()` is true | 2 |
| `resources/views/content/Fuelstation/index.blade.php` | 12-17 | `$total` is accumulated by a manual `foreach`/`+=` loop over `$fuelStations` directly in the Blade template (business logic in view) instead of being computed by the controller/repository; duplicated verbatim in `test.blade.php:12-17`; also wrong once pagination is added, since it only sums the current page, not the full filtered set (label says "Total Amount" with no page-scope caveat) | 3 |
| `resources/views/content/dashboard/index.blade.php` | 108, 149(fmt only), 220, 273, 280, 287, 294 | Percentage/ratio math (`$TotalPaidDebt/$TotalDebt`, `$TotalAmountTypeDiesel/$TotalFuel`, etc.) computed inline in Blade with no divide-by-zero guard — if any denominator total is `0` (e.g. no debts yet), PHP emits a warning and yields `INF`/`NAN`, which `number_format()` then renders as garbage in the UI | 3 |
| `resources/views/content/dashboard/index2.blade.php` | 21, 42, 282-285 | Same inline percentage math, same divide-by-zero exposure, duplicated independently from `index.blade.php` | 3 |
| `resources/views/content/dashboard/index.blade.php`, `resources/views/content/dashboard/index2.blade.php` | whole files | `index.blade.php` (465 LOC) and `index2.blade.php` (373 LOC) render near-identical KPI cards and ApexCharts widgets (debt progress, fuel type distribution, debt timeline, fuel timeline) fed by parallel controller actions (`Analytics@index` / `Analytics@index2`) — large-scale duplication of both markup and inline JS instead of one parametrized view or shared partials/components | 3 |
| `resources/views/content/dashboard/dashboards-analytics.blade.php` | whole file (461 LOC) | Only ever referenced from **commented-out** `view()` calls (`app/Http/Controllers/dashboard/Analytics.php:50,117`) — dead view, unreachable from any route | 2 |
| `resources/views/content/dashboard/temp.blade.php` | whole file | Rendered by `TLDRController` ("TL;DR / Text summarization" feature), which does not appear anywhere in `docs/audit/routes.json` — the controller/view pair is orphaned (unroutable), and the form inside posts to `url('/tldr/action')`, itself not a registered route | 3 |
| `resources/views/content/Debt/test.blade.php`, `resources/views/content/Fuelstation/test.blade.php` | whole files | Neither is referenced by any controller `view()` call or any other view's `@include` (confirmed via repo-wide grep) — dead scratch/dev copies of `edit.blade.php` and `index.blade.php` respectively, left in the tracked views tree | 2 |
| `resources/views/content/pages/*.blade.php` (5 files, 566 LOC total) | whole files | None of `pages-account-settings-account`, `-connections`, `-notifications`, `pages-misc-error`, `pages-misc-under-maintenance` are referenced by any controller or route (`grep` of `docs/audit/routes.json` and `app/Http/Controllers` for "pages" and "account-settings" returns nothing) — unrouted template-scaffold boilerplate (hardcoded "John Doe" demo data) left over from the Sneat admin template | 2 |
| `resources/views/content/Subcategory/index.blade.php` | whole file (12 LOC) | Placeholder stub (`<h1>Hello</h1>`), not a real page; not referenced anywhere either (`services.subcategory.index` route exists per `routes.json` but no controller/view mapping to this file was found in scanned controllers) | 2 |
| `app/Http/Controllers/FuelStation/FulstationController.php` | 44 vs 97 vs 104 | Same controller calls `view('content.fuelstation.index', ...)` (lowercase, lines 44 and 97) and `view('content.Fuelstation.index', ...)` (capital, line 104) inconsistently for what should be the same view — only the capitalized path exists on disk; the lowercase calls will 404 on a case-sensitive filesystem (same class of bug documented for `Debt`/`DebtWithSupplier` in `docs/audit/raw/01-debt-billing.md`, and equally present here for `content.category.index` at `app/Http/Controllers/Category/CategoryController.php:27` vs tracked path `content/Category/index.blade.php`) | 4 |
| `resources/views/content/Debt/edit.blade.php`, `.../DebtWithSupplier/edit.blade.php` | whole files (292 LOC each) | Byte-for-byte identical except the target route name (`debt.update` vs `debt-supplier.update`) — confirmed via `diff`; same duplication for `payDebt.blade.php` (189 LOC each, single-line diff) and near-total duplication for `index.blade.php`/`indexPaid.blade.php`/`create.blade.php`/`view.blade.php`/`delete.blade.php` pairs — an entire parallel view tree exists solely to point forms at a different route prefix and add one extra column (`tractorDriver->fullname`) | 3 |
| `resources/views/content/DebtWithSupplier/index.blade.php` | ~55, ~70 | Unlike `content/Debt/index.blade.php` (which uses `number_format($item->amount, 2)` / `number_format($debt->total_debt_amount, 2)` throughout), this near-duplicate view renders `{{ $item->amount }}` / `{{ $debt->total_debt_amount }}` raw, without `number_format()` — inconsistent amount formatting between the two otherwise-parallel pages | 2 |
| `resources/views/content/Debt/edit.blade.php` | 187, 245 | `url: '{{ route('services.subcategory.show', '01') }}'` — the route parameter is a hardcoded literal `'01'` that is never actually consumed server-side (the real `id` is passed separately as an AJAX query-string param); works only because the controller ignores the route-bound id and reads `request('id')` instead — a fragile, easily-misread pattern; repeated identically in `Debt/index.blade.php:238`, `Debt/indexPaid.blade.php:275`, `DebtWithSupplier/edit.blade.php:187,245`, `DebtWithSupplier/index.blade.php:239`, `DebtWithSupplier/indexPaid.blade.php:234` (8 occurrences total) | 2 |
| `resources/views/content/Vehicle/edit.blade.php` | 34-42 | `@php` block parses `$vehicle->license_plate` (a single string column, e.g. `"L - Y - W"`) via `explode(' - ', ...)` to recover 3 sub-fields for display — business/domain logic (decoding a composite field format) embedded in the view instead of a model accessor; duplicated verbatim in `Vehicle/added-date.blade.php:32-40`. Correctness currently depends on this parse order exactly matching the concatenation order in `app/Http/Controllers/Vehicle/VehicleController.php:57,140` (`license . ' - ' . year_license . ' - ' . wilaya_license`) — any future change to one side silently breaks the other with no test coverage | 3 |
| `resources/views/content/Fuelstation/pagination-data.blade.php` | 28-40 | `@switch($fuelStation->vehicle->type)` badge-icon logic duplicated verbatim in `resources/views/content/Vehicle/index.blade.php:36-46` and `resources/views/content/Vehicle/added-date.blade.php:19-29` — same 3-case vehicle-type-to-icon mapping copy-pasted 3 times instead of a shared partial/component or a model accessor | 2 |
| `app/Http/Controllers/FuelStation/FulstationController.php` | (no `with()` calls found) | `$fuelStation->vehicle` is accessed per row in `pagination-data.blade.php:28,41-42`, `delete.blade.php:5,12`, and `paided.blade.php:5` (each `@include`-d once per row from `index`) with no eager-loading (`with('vehicle')`) found in the controller — classic N+1 risk, one query per fuel-station row | 3 |
| `resources/views/content/Debt/index.blade.php`, `.../indexPaid.blade.php`, `.../DebtWithSupplier/index.blade.php`, `.../indexPaid.blade.php`, `.../TractorDriver/index.blade.php`, `.../Vehicle/index.blade.php` | whole files | Every index page repeats the same ~50-line inline `<script>` DataTables `initComplete` block (per-column footer search input wiring) and the same status-filter Arabic/English `switch` — 6× duplicated instead of one shared JS module/partial | 2 |
| `resources/views/content/TractorDriver/index.blade.php`, `.../Vehicle/index.blade.php` | 27 / 19 | Both use `id="datatable-debt"` for their `<table>` (and the matching `new DataTable('#datatable-debt', ...)` init) — copy-pasted from the Debt views; harmless while each page is standalone, but is a copy-paste smell and would break if two such tables ever appeared on the same page | 1 |
| `resources/views/layouts/sections/menu/submenu.blade.php` | 6-28 | `@php` block implements "is this menu item active" logic (string matching on `Route::currentRouteName()`, looping over array-or-scalar `$submenu->slug`) directly in the partial — UI-state business logic in Blade; a near-identical, larger version is also present, dead/commented-out, in `verticalMenu.blade.php:178-200` | 2 |
| `resources/views/content/authentications/auth-register-basic.blade.php`, `auth-forgot-password-basic.blade.php` | whole files | Both pages exist and are routed (`auth-register-basic`, `auth-reset-password-basic` in `docs/audit/routes.json`), but `auth-login-basic.blade.php:60-65` has the "Create an account" link commented out — register page is reachable only by typing the URL directly, no discoverable link from login | 1 |

## Open Questions

- Whether `resources/views/content/Category/destroy.blade.php` is genuinely dead (no `@include` or `view()` reference found repo-wide in the files read) or reachable through a code path outside this scope (e.g. a Livewire/JS component not inspected) could not be fully confirmed without reading every controller in the repo.
- The actual runtime behavior of the `route('building-materals.*')` mismatches (severity 5 above) — whether these forms have simply never been clicked in production, or whether route-caching/aliasing elsewhere papers over the mismatch — could not be verified without route-cache inspection or a running instance.
- `app/Http/Controllers/TLDRController.php` and its `/tldr/action` target were found only by tracing `content.dashboard.temp.blade.php`; the controller itself, its registration (or lack thereof), and whether it's intentionally being removed were not investigated further — out of the assigned view-scope's file list, only cited for the dead-view finding.
- Root-level `lang/` directory (Laravel ≥9 style, distinct from the assigned-but-absent `resources/lang/`) was not opened — unclear whether it contains real translation files backing the pervasive `__('...')` calls in every view, or is empty/unused.
- Whether `toastr()` flash messages (called from controllers per other scopes) actually render anywhere in this layout — no toastr container `<div>` or vendor `<script>` tag was found in `layouts/sections/scripts.blade.php` or `scriptsIncludes.blade.php`; could not confirm without reading `public/assets/vendor` contents or a wider grep across `resources/assets`, which is outside this scope's `resources/views`/`resources/lang`/`public` (top-level only) boundary.
- Whether the N+1 risk on `$fuelStation->vehicle` (Smells table) is masked by eager-loading declared on the `FuelStation` model itself (e.g. `protected $with = ['vehicle']`) was not checked, since `app/Models/FuelStation.php` is outside this view-only scope.
