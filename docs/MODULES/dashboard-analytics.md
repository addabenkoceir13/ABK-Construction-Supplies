# Module: Dashboard & Analytics

KPI aggregation over Debt and Fuel Station data. Two live, independently-maintained dashboards render
overlapping but not-identical numbers for the same underlying data. Three additional controllers in this
scope are dead code.

## Files

| File | LOC |
|---|---:|
| `app/Http/Controllers/dashboard/Analytics.php` | 120 |
| `app/Http/Controllers/dashboard/Crm.php` | 14 |
| `app/Http/Controllers/dashboard/Ecommerce.php` | 13 |
| `app/Http/Controllers/TLDRController.php` | 18 |

Reads (not owned by this module, but consumed): `app/Models/Debt.php`, `app/Models/FuelStation.php`.

## Responsibilities

### `Analytics` (`app/Http/Controllers/dashboard/Analytics.php:11`)
**No constructor, no injected dependencies at all** — every call is a static model method
(`ARCHITECTURE.md` V4). Dead imports: `Illuminate\Http\Request` and `Auth`, neither referenced in the class
body (`:8-9`).

| Method | Route | Line | Behavior |
|---|---|---|---|
| `index()` | `GET /template`, name `dashboard-analytics-template` | `:13-52` | 13 static aggregate calls + `Debt::getDebtTimeline()` (model-encapsulated) + `FuelStation::getMonthlyFuelData()` (model-encapsulated) → `content.dashboard.index` |
| `index2()` | `GET /`, name `dashboard-analytics` | `:53-119` | Same 13 static calls (byte-for-byte duplicated) + **two inline raw `selectRaw` timeline queries written directly in the controller** → `content.dashboard.index2` |

Both routes are live and gated by `auth` middleware. `index2()` is the actual root/home dashboard.

**The two dashboards diverge on the debt timeline.** `index()` delegates to `Debt::getDebtTimeline()`
(`app/Models/Debt.php:54-67`), which groups by `date_debut_debt`. `index2()`'s inline query (`:71-84`) groups
by `date_end_debt` instead. Same report, two different group-by columns — **the two dashboards show
different numbers for what looks like the same chart** (`ARCHITECTURE.md` V5).

A commented-out line (`:48`) references `Debt::getDriverDebts()` — a method that does not exist anywhere on
the `Debt` model. Dead/aspirational code.

### 13 static aggregate calls, per request, per dashboard route
`Debt::getTotalDebt/getTotalPaidDebt/getTotalRestDebt` (`app/Models/Debt.php:40-52`) —
each an unfiltered `sum()` combining client and supplier debt (see `MODULES/debt-billing.md`). Plus 10 on
`FuelStation`: `getTotalPaidFuel/getTotalUnPaidFuel/getTotalFuel/getTotalLiter/getTotalLiterTypeDiesl
/getTotalLiterGas/getTotalLiterGasoline/getTotalAmountTypeDiesel/getTotalAmountGas/getTotalAmountGasoline`
(`app/Models/FuelStation.php:51-97`) — **10 separate `SELECT SUM(...)` round-trips against `fuel_stations`**
instead of one grouped/conditional-aggregate query. Neither dashboard route caches any of this
(`config/cache.php` defaults to `file`, but `grep -rn "Cache::\|cache("` across `app/`/`routes/` returns zero
hits — `ARCHITECTURE.md` §4) — all 13 queries run fresh on every hit to `/` or `/template`.

### `Crm`, `Ecommerce`, `TLDRController` — dead code
None of the three are reachable via any route (`grep` of `routes/web.php`, `routes/api.php`, and the live
`route:list` snapshot all return zero matches). `Crm::index()` and `Ecommerce::index()` additionally target
Blade views that **do not exist** (`content.dashboard.dashboards-crm`, `content.dashboard.dashboards-ecommerce`
— confirmed via glob) — they would throw `ViewNotFoundException` even if a route were added.
`TLDRController::action()` is a bare `dd($request)` stub with no real implementation.

## Data flow

`GET /` → `Analytics@index2` → 13 static aggregate calls + 2 inline raw-SQL timeline queries → assembles a
19-key `$data` array (`:86-114`) → `view('content.dashboard.index2', $data)`.

`GET /template` → `Analytics@index` → same 13 static calls + 2 model-encapsulated timeline calls →
`view('content.dashboard.index', $data)`.

Both views (`resources/views/content/dashboard/index.blade.php`, 465 LOC, and `index2.blade.php`, 373 LOC)
independently re-derive percentages from the totals with **no divide-by-zero guard** — if any denominator is
zero (e.g. no debts recorded yet), PHP emits a warning and yields `INF`/`NAN`, which `number_format()` then
renders as garbage in the UI (`docs/audit/raw/08-frontend-views.md`).

## Known issues

| Issue | Where | Severity |
|---|---|---|
| Two dashboards' debt timelines group by different columns — diverging numbers for "the same" chart | `Analytics.php:47` vs `:71-84` | 3 |
| `index()`/`index2()` duplicate the same 13 aggregate-fetch statements verbatim | `Analytics.php:15-28,55-68` | 3 |
| 10 separate `SUM()` queries against `fuel_stations` instead of one grouped query, uncached | `Analytics.php:19-28,59-68` | 3 |
| `Crm`/`Ecommerce` render views that don't exist | `Crm.php:12`, `Ecommerce.php:11` | 3 |
| `Crm`, `Ecommerce`, `TLDRController` entirely unreachable | all three files | 2 |
| Dead imports, commented-out call to a nonexistent method | `Analytics.php:8-9,48` | 1 |

No fat-controller violation by LOC (`Analytics.php` is 120 LOC, under the 150-line threshold used elsewhere
in this audit), no direct `DB::` facade usage, no missing-transaction risk (everything here is read-only).
Full list: `docs/audit/raw/06-dashboard-analytics.md`.

## Open questions

- Is `Analytics@index2` (route `/`) the intended primary dashboard and `Analytics@index` (route `/template`)
  a legacy/staging duplicate, or vice versa? Both are live with diverging implementations — not determinable
  from code alone.
- Are `Crm`, `Ecommerce`, `TLDRController` intentionally scaffolded-but-unfinished, or abandoned code safe to
  delete? Needs product/team confirmation.
- Do `Debt`'s unfiltered aggregates represent an intentional combined client+supplier KPI, or a bug? See
  `ARCHITECTURE.md` §7 — same open question, this module is where it surfaces to end users.
