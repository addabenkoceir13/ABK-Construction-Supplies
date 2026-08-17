# Scope: Dashboard & Analytics

## Files
| File | LOC |
|---|---|
| app/Http/Controllers/dashboard/Analytics.php | 120 |
| app/Http/Controllers/dashboard/Crm.php | 14 |
| app/Http/Controllers/dashboard/Ecommerce.php | 13 |
| app/Http/Controllers/TLDRController.php | 18 |

Related (read for data-flow tracing, not in edit scope):
- app/Models/Debt.php (75 LOC) — static aggregate helpers consumed by Analytics
- app/Models/FuelStation.php (113 LOC) — static aggregate helpers consumed by Analytics
- app/Http/Controllers/Controller.php (13 LOC) — base controller (AuthorizesRequests, DispatchesJobs, ValidatesRequests traits only)
- routes/web.php (107 LOC)
- docs/audit/routes.json (route:list snapshot)

## Classes & Responsibilities

### `App\Http\Controllers\dashboard\Analytics` — app/Http/Controllers/dashboard/Analytics.php:11
- Extends `App\Http\Controllers\Controller` (app/Http/Controllers/dashboard/Analytics.php:11)
- No traits, no constructor, no injected dependencies (uses static model calls directly).
- Imports `Illuminate\Http\Request` (app/Http/Controllers/dashboard/Analytics.php:8) and `Auth` (app/Http/Controllers/dashboard/Analytics.php:9) — neither is referenced anywhere in the class body (dead imports).
- `public function index()` — app/Http/Controllers/dashboard/Analytics.php:13-52. Purpose: build the "template" dashboard dataset (debt totals, fuel totals/breakdowns, debt timeline, fuel-monthly series) and render `content.dashboard.index`.
- `public function index2()` — app/Http/Controllers/dashboard/Analytics.php:53-119. Purpose: build the "main" dashboard dataset — duplicates all of `index()`'s aggregate calls, then additionally runs two inline raw SQL timeline queries (debt + fuel) and renders `content.dashboard.index2`.

### `App\Http\Controllers\dashboard\Crm` — app/Http/Controllers/dashboard/Crm.php:8
- Extends `App\Http\Controllers\Controller`. No traits, no constructor.
- `public function index()` — app/Http/Controllers/dashboard/Crm.php:10-13. Purpose: render `content.dashboard.dashboards-crm`.

### `App\Http\Controllers\dashboard\Ecommerce` — app/Http/Controllers/dashboard/Ecommerce.php:8
- Extends `App\Http\Controllers\Controller`. No traits, no constructor.
- `public function index()` — app/Http/Controllers/dashboard/Ecommerce.php:10-12. Purpose: render `content.dashboard.dashboards-ecommerce`.

### `App\Http\Controllers\TLDRController` — app/Http/Controllers/TLDRController.php:8
- Extends `App\Http\Controllers\Controller`. No traits, no constructor.
- `public function index()` — app/Http/Controllers/TLDRController.php:10-13. Purpose: render `content.dashboard.temp`.
- `public function action(Request $request)` — app/Http/Controllers/TLDRController.php:15-17. Purpose: `dd($request)` — dumps the request and halts execution; no real behavior implemented.

## Data Flow (entrypoint -> exit)

**`GET /` → `Analytics@index2`** (routes/web.php:47, confirmed live in docs/audit/routes.json:1 as route name `dashboard-analytics`, middleware `web`, `App\Http\Middleware\Authenticate`)
1. Calls 12 static aggregate methods on `Debt`/`FuelStation` (app/Http/Controllers/dashboard/Analytics.php:55-68): `Debt::getTotalDebt/getTotalPaidDebt/getTotalRestDebt` (app/Models/Debt.php:40-52), `FuelStation::getTotalPaidFuel/getTotalUnPaidFuel/getTotalFuel/getTotalLiter/getTotalLiterTypeDiesl/getTotalLiterGas/getTotalLiterGasoline/getTotalAmountTypeDiesel/getTotalAmountGas/getTotalAmountGasoline` (app/Models/FuelStation.php:51-97) — each is an independent `SELECT SUM(...)` query, so this is **10 separate full-table aggregate queries against `fuel_stations`** plus 3 against `debt` (13 round-trips total) instead of one grouped query.
2. Runs two additional inline raw-SQL aggregate queries directly in the controller (app/Http/Controllers/dashboard/Analytics.php:71-84): a `Debt::selectRaw(...)` grouped-by-year/month debt timeline, and a `FuelStation::selectRaw(...)` grouped-by-year/month fuel timeline — business/reporting logic embedded in the controller rather than in the model (contrast with `index()`, which delegates the equivalent query to `Debt::getDebtTimeline()`, app/Models/Debt.php:54-67).
3. Assembles a 19-key `$data` array (app/Http/Controllers/dashboard/Analytics.php:86-114) and returns `view('content.dashboard.index2', $data)` (app/Http/Controllers/dashboard/Analytics.php:118). View file exists at resources/views/content/dashboard/index2.blade.php.

**`GET /template` → `Analytics@index`** (routes/web.php:48, confirmed live in docs/audit/routes.json line 1 area as route name `dashboard-analytics-template`, middleware `web`, `App\Http\Middleware\Authenticate`)
1. Same 13 static aggregate calls as above, byte-for-byte duplicated (app/Http/Controllers/dashboard/Analytics.php:15-28 vs 55-68).
2. Adds `Debt::getDebtTimeline()` (app/Models/Debt.php:54-67) and `FuelStation::getMonthlyFuelData()` (app/Models/FuelStation.php:99-112) — both delegate the raw SQL to the model, unlike `index2()`.
3. A commented-out line (app/Http/Controllers/dashboard/Analytics.php:48) references `Debt::getDriverDebts()`, a method that does not exist anywhere in `app/Models/Debt.php` — dead/aspirational code.
4. Returns `view('content.dashboard.index', $data)` (app/Http/Controllers/dashboard/Analytics.php:51). View file exists at resources/views/content/dashboard/index.blade.php.

**`Crm@index` and `Ecommerce@index` — NOT reachable via any route.** Neither `dashboard-crm`/`dashboard-ecommerce` nor any controller path containing `Crm`/`Ecommerce` appears in routes/web.php, routes/api.php, or docs/audit/routes.json (the live `php artisan route:list` snapshot). These two controllers are dead code as currently wired.

**`TLDRController` — NOT reachable via any route.** No route references `TLDRController` in routes/web.php or docs/audit/routes.json. Dead code.

## External Dependencies (packages, APIs, queues)
- None found directly in these 4 controllers — no HTTP clients, no queue dispatches, no third-party SDK calls, no jobs/listeners/events referenced.
- Downstream model dependencies: `Askedio\SoftCascade\Traits\SoftCascadeTrait` (app/Models/Debt.php:5, app/Models/FuelStation.php:5) and `Laravel\Scout\Searchable` (app/Models/FuelStation.php:9) are used by the models these controllers call into, but neither package is invoked by the controller code itself.
- No FormRequests, Policies, Jobs, or Listeners are used by any of the 4 controllers in scope.
- Middleware: both live routes (`/` and `/template`) are gated by `auth` middleware, resolving to `App\Http\Middleware\Authenticate` (docs/audit/routes.json, routes/web.php:47-48).

## Smells & Debt

| file | line | issue | severity 1-5 |
|---|---|---|---|
| app/Http/Controllers/dashboard/Analytics.php | 15-28, 55-68 | `index()` and `index2()` duplicate the exact same 13 static aggregate-fetch statements verbatim — no shared private method/service to build the base dataset | 3 |
| app/Http/Controllers/dashboard/Analytics.php | 19-28, 59-68 (10 calls) | 10 separate `SELECT SUM(...)` queries against `fuel_stations` per request (`getTotalPaidFuel`, `getTotalUnPaidFuel`, `getTotalFuel`, `getTotalLiter`, `getTotalLiterTypeDiesl`, `getTotalLiterGas`, `getTotalLiterGasoline`, `getTotalAmountTypeDiesel`, `getTotalAmountGas`, `getTotalAmountGasoline`) instead of one grouped/conditional-aggregate query — both `index()` and `index2()` pay this cost on every hit to `/` and `/template` | 3 |
| app/Http/Controllers/dashboard/Analytics.php | 71-84 | Raw `selectRaw` reporting queries (debt timeline, fuel timeline) written directly in the controller in `index2()`, duplicating logic that already exists properly encapsulated as `Debt::getDebtTimeline()` (app/Models/Debt.php:54-67) and `FuelStation::getMonthlyFuelData()` (app/Models/FuelStation.php:99-112), which `index()` uses instead — same report, two divergent implementations | 3 |
| app/Http/Controllers/dashboard/Analytics.php | 8-9 | Unused imports `Illuminate\Http\Request` and `Auth` — neither is referenced in the class | 1 |
| app/Http/Controllers/dashboard/Analytics.php | 48 | Commented-out call to `Debt::getDriverDebts()` — method does not exist on the `Debt` model (verified: absent from app/Models/Debt.php) | 1 |
| app/Http/Controllers/dashboard/Crm.php | 10-13 | Controller and its `index()` action are unreachable — no matching route in routes/web.php, routes/api.php, or docs/audit/routes.json | 2 |
| app/Http/Controllers/dashboard/Crm.php | 12 | Returns `view('content.dashboard.dashboards-crm')` but no such Blade file exists anywhere in the project (confirmed via glob for `**/dashboards-crm*`) — would throw `ViewNotFoundException` if ever routed to | 3 |
| app/Http/Controllers/dashboard/Ecommerce.php | 10-12 | Controller and its `index()` action are unreachable — no matching route anywhere | 2 |
| app/Http/Controllers/dashboard/Ecommerce.php | 11 | Returns `view('content.dashboard.dashboards-ecommerce')` but no such Blade file exists anywhere in the project (confirmed via glob for `**/dashboards-ecommerce*`) — would throw `ViewNotFoundException` if ever routed to | 3 |
| app/Http/Controllers/TLDRController.php | 8-18 | Entire controller (both actions) is unreachable — no route references `TLDRController` anywhere | 2 |
| app/Http/Controllers/TLDRController.php | 15-17 | `action()` method body is `dd($request)` — a debug dump-and-die stub with no real implementation, left in source | 2 |

No fat-controller violation by LOC (largest is Analytics.php at 120 LOC, under the 150 threshold), no N+1 relation-loading risk (all queries are aggregate `SUM`/`groupBy`, not relation lazy-loading), no direct `DB::` facade usage, no missing-transaction risk (all operations are read-only `SELECT`s), and no business logic found in Blade/Livewire for this scope — the queries live in controllers/models, not views.

## Open Questions
- Is `Analytics@index2` (route `/`, name `dashboard-analytics`) the intended primary dashboard and `Analytics@index` (route `/template`, name `dashboard-analytics-template`) a legacy/staging duplicate, or vice versa? Both are live and independently maintained with diverging timeline-query implementations — could not determine intent from code alone.
- Are `Crm`, `Ecommerce`, and `TLDRController` intentionally scaffolded-but-unfinished features, or abandoned code that should be removed? Not resolvable from static analysis; would need product/team confirmation.
- Since `Crm@index` and `Ecommerce@index` target Blade views that do not exist in the repository, was a resource/view file deleted separately from the controller, or were the controllers generated ahead of the views and never followed up? Could not determine from git history (out of scope for this static, read-only pass).
