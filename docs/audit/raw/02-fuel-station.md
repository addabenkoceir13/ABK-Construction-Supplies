# Scope: Fuel Station

## Files

| File | LOC |
|---|---|
| app/Http/Controllers/FuelStation/FulstationController.php | 260 |
| app/Models/FuelStation.php | 113 |
| app/Repositories/FuelStation/FuelStationRepository.php | 51 |
| app/Repositories/FuelStation/EloquentFuelStation.php | 136 |

Total in-scope: 4 files, 560 LOC.

## Classes & Responsibilities

### `App\Http\Controllers\FuelStation\FulstationController` — app/Http/Controllers/FuelStation/FulstationController.php:13
- Extends `App\Http\Controllers\Controller` (app/Http/Controllers/FuelStation/FulstationController.php:13).
- No traits, no interfaces implemented.
- Constructor-injected dependencies (app/Http/Controllers/FuelStation/FulstationController.php:18-22):
  - `FuelStationRepository $fuelStation` — app/Http/Controllers/FuelStation/FulstationController.php:18
  - `VehicleRepository $vehicle` — app/Http/Controllers/FuelStation/FulstationController.php:18
- Public methods:
  - `index(Request $request)` — app/Http/Controllers/FuelStation/FulstationController.php:24 — lists unpaid fuel receipts (paginated, search/date filters), returns JSON partial on AJAX or full view otherwise.
  - `indexA(Request $request)` — app/Http/Controllers/FuelStation/FulstationController.php:47 — DataTables-style server-side search/pagination endpoint querying `FuelStation` directly.
  - `indexPaid(Request $request)` — app/Http/Controllers/FuelStation/FulstationController.php:100 — lists paid fuel receipts.
  - `create()` — app/Http/Controllers/FuelStation/FulstationController.php:113 — empty stub (no-op).
  - `store(Request $request)` — app/Http/Controllers/FuelStation/FulstationController.php:119 — validates and creates a fuel receipt inside a DB transaction.
  - `show($id)` — app/Http/Controllers/FuelStation/FulstationController.php:158 — empty stub (no-op).
  - `edit($id)` — app/Http/Controllers/FuelStation/FulstationController.php:169 — empty stub (no-op).
  - `update(Request $request, $id)` — app/Http/Controllers/FuelStation/FulstationController.php:175 — validates and updates a fuel receipt inside a DB transaction.
  - `destroy($id)` — app/Http/Controllers/FuelStation/FulstationController.php:208 — soft-deletes a fuel receipt inside a DB transaction.
  - `status(Request $request, $id)` — app/Http/Controllers/FuelStation/FulstationController.php:225 — sets a single record's `status` field from raw request input.
  - `updateStatus(Request $request)` — app/Http/Controllers/FuelStation/FulstationController.php:244 — bulk-sets `status = 'paid'` for an array of ids.

### `App\Models\FuelStation` — app/Models/FuelStation.php:11
- Extends `Illuminate\Database\Eloquent\Model`.
- Traits: `HasFactory`, `SoftDeletes`, `Askedio\SoftCascade\Traits\SoftCascadeTrait`, `Laravel\Scout\Searchable` (app/Models/FuelStation.php:13).
- `$fillable`: vehicle_id, name_owner, name_driver, name_distributor, filing_datetime, liter, amount, status, type_fuel (app/Models/FuelStation.php:15-25).
- Relations:
  - `vehicle()` — `belongsTo(Vehicle::class)` — app/Models/FuelStation.php:28-31.
- `toSearchableArray()` — app/Models/FuelStation.php:38-49 — Scout indexable payload (name_owner, name_driver, name_distributor, liter, amount, status, type_fuel).
- Static aggregate helpers (all thin query wrappers, no business logic): `getTotalPaidFuel()` (:51-54), `getTotalUnPaidFuel()` (:55-58), `getTotalFuel()` (:60-63), `getTotalLiter()` (:65-68), `getTotalLiterTypeDiesl()` (:69-72, note misspelling "Diesl"), `getTotalLiterGas()` (:74-77), `getTotalLiterGasoline()` (:79-82), `getTotalAmountTypeDiesel()` (:84-87), `getTotalAmountGas()` (:89-92), `getTotalAmountGasoline()` (:94-97), `getMonthlyFuelData()` (:99-112, uses `selectRaw` grouping by YEAR/MONTH/type_fuel).
- No casts, no scopes, no observers/events registered in this file.

### `App\Repositories\FuelStation\FuelStationRepository` (interface) — app/Repositories/FuelStation/FuelStationRepository.php:5
- Declares: `all()`, `find($id)`, `updateStatus($ids, $status)`, `create(array $data)`, `update($id, array $data)`, `delete($id)`, `paginate($perPage, $search, $start_date, $end_date)`, `paginatePaid($perPage, $search, $start_date, $end_date)`.
- Docblocks reference "Coupon" (copy-pasted from an unrelated repository) — app/Repositories/FuelStation/FuelStationRepository.php:8, :35, :44.

### `App\Repositories\FuelStation\EloquentFuelStation` (implementation) — app/Repositories/FuelStation/EloquentFuelStation.php:10
- Implements `FuelStationRepository`.
- No constructor dependencies; calls `App\Models\FuelStation` statically throughout.
- `all()` — app/Repositories/FuelStation/EloquentFuelStation.php:15-18 — `FuelStation::all()`.
- `find($id)` — app/Repositories/FuelStation/EloquentFuelStation.php:23-26.
- `updateStatus($ids, $status)` — app/Repositories/FuelStation/EloquentFuelStation.php:28-31 — `whereIn('id', $ids)->update(...)`.
- `create(array $data)` — app/Repositories/FuelStation/EloquentFuelStation.php:37-42.
- `update($id, array $data)` — app/Repositories/FuelStation/EloquentFuelStation.php:47-54 — `find()` then `update()`.
- `delete($id)` — app/Repositories/FuelStation/EloquentFuelStation.php:59-64 — `find()` then `delete()` (soft delete via model trait).
- `paginate($perPage, $search, $start_date, $end_date)` — app/Repositories/FuelStation/EloquentFuelStation.php:71-106 — branches between Scout `FuelStation::search()` (when `$search` set) and a plain Eloquent query (date range + `status = 'unpaid'` filter), both paginated.
- `paginatePaid($perPage, $search, $start_date, $end_date)` — app/Repositories/FuelStation/EloquentFuelStation.php:107-135 — plain Eloquent query with `orWhere` search across several columns + `status = 'paid'` filter, paginated.
- Binding: `App\Providers\EloquentRepositoryProvider.php:42` binds `FuelStationRepository::class` → `EloquentFuelStation::class`.

## Data Flow (entrypoint -> exit)

- `GET fuel-stations` (name `fuel-stations.index`) → `FulstationController::index` (app/Http/Controllers/FuelStation/FulstationController.php:24) → `FuelStationRepository::paginate()` (app/Repositories/FuelStation/EloquentFuelStation.php:71) → Eloquent/Scout query on `FuelStation` → view `content.fuelstation.index` (non-AJAX) or JSON with rendered partial `content.Fuelstation.pagination-data` (AJAX, app/Http/Controllers/FuelStation/FulstationController.php:38).
- `GET fuel-stations/search` (name `fuel-stations.index-search`) → `FulstationController::indexA` (app/Http/Controllers/FuelStation/FulstationController.php:47) → direct `FuelStation::query()` (bypasses repository) → JSON DataTables payload, each row rendering partial `content.Fuelstation.partials.actions` (app/Http/Controllers/FuelStation/FulstationController.php:88).
- `GET fuel-stations/status/paid` (name `fuel-stations.index-paid`) → `FulstationController::indexPaid` (app/Http/Controllers/FuelStation/FulstationController.php:100) → `FuelStationRepository::paginatePaid()` (app/Repositories/FuelStation/EloquentFuelStation.php:107) → view `content.Fuelstation.index`.
- `POST fuel-stations` (name `fuel-stations.store`) → `FulstationController::store` (app/Http/Controllers/FuelStation/FulstationController.php:119) → `Validator` → `DB::beginTransaction()` → `FuelStationRepository::create()` (app/Repositories/FuelStation/EloquentFuelStation.php:37) → `DB::commit()`/`rollBack()` → redirect back with `toastr()` flash.
- `PUT|PATCH fuel-stations/{fuel_station}` (name `fuel-stations.update`) → `FulstationController::update` (app/Http/Controllers/FuelStation/FulstationController.php:175) → same validate/transaction pattern → `FuelStationRepository::update()`.
- `DELETE fuel-stations/{fuel_station}` (name `fuel-stations.destroy`) → `FulstationController::destroy` (app/Http/Controllers/FuelStation/FulstationController.php:208) → transaction → `FuelStationRepository::delete()` (soft delete).
- `PATCH fuel-stations/status/{id}` (name `fuel-stations.status`) → `FulstationController::status` (app/Http/Controllers/FuelStation/FulstationController.php:225) → transaction → repository `find()` + direct model `save()` of unvalidated `$request->status`.
- `POST fuel-stations/change-status` (name `fuel-stations.update.status`) → `FulstationController::updateStatus` (app/Http/Controllers/FuelStation/FulstationController.php:244) → transaction → `FuelStationRepository::updateStatus($ids, 'paid')` (bulk update, unvalidated `$request->ids`).
- `GET fuel-stations/{fuel_station}` (show), `GET fuel-stations/create` (create), `GET fuel-stations/{fuel_station}/edit` (edit) → controller stubs, no-op (app/Http/Controllers/FuelStation/FulstationController.php:113-116, :158-161, :169-172).
- Cross-module: `App\Models\Vehicle` declares `$softCascade = ['insuranceVehicle', 'fuelStations']` and `hasMany(FuelStation::class)` (app/Models/Vehicle.php:21-25), so soft-deleting a `Vehicle` cascades soft-deletes into `FuelStation` rows via the `Askedio\SoftCascade` package.

## External Dependencies (packages, APIs, queues)

- `Askedio\SoftCascade\Traits\SoftCascadeTrait` — app/Models/FuelStation.php:5, :13 (cascade soft-delete package).
- `Laravel\Scout\Searchable` — app/Models/FuelStation.php:9, :13, used in `EloquentFuelStation::paginate()` via `FuelStation::search()` (app/Repositories/FuelStation/EloquentFuelStation.php:75). Search driver not verified in this scope (see Open Questions).
- `toastr()` helper (flash-message package, likely `brian2694/laravel-toastr` or similar) — used throughout the controller for user-facing success/error messages (app/Http/Controllers/FuelStation/FulstationController.php:133, :143, :147, :189, :199, :203, :216, :220, :235, :239, :252, :256).
- `Illuminate\Support\Facades\DB` — used only for `beginTransaction`/`commit`/`rollBack` (app/Http/Controllers/FuelStation/FulstationController.php:10, :138, :142, :146, :194, :198, :202, :211, :215, :219, :228, :234, :238, :247, :251, :255).
- `Illuminate\Support\Facades\Validator` — inline validation, no `FormRequest` classes used (app/Http/Controllers/FuelStation/FulstationController.php:11, :121, :177).
- `App\Repositories\Vehicle\VehicleRepository` — cross-module dependency for the vehicle picklist (app/Http/Controllers/FuelStation/FulstationController.php:8, :16, :21).
- No queues, jobs, listeners, policies, or FormRequest classes found for this scope.

## Smells & Debt

| file | line | issue | severity 1-5 |
|---|---|---|---|
| app/Http/Controllers/FuelStation/FulstationController.php | 44, 97 | `view('content.fuelstation.index', ...)` uses lowercase `fuelstation`, but the actual view directory on disk is `resources/views/content/Fuelstation` (capital F, confirmed via glob). Works on case-insensitive filesystems (Windows dev) but will 500/`ViewNotFoundException` on case-sensitive filesystems (typical Linux prod). `indexPaid()` at line 104 correctly uses `content.Fuelstation.index`. | 5 |
| app/Http/Controllers/FuelStation/FulstationController.php | 88 | `view('content.Fuelstation.partials.actions', ...)` referenced inside `indexA()`, but no `resources/views/content/Fuelstation/partials/` directory exists at all (confirmed via glob — zero matches). Any hit to `fuel-stations/search` with AJAX will throw `ViewNotFoundException`. | 5 |
| routes/web.php | 71-75 | `Route::resource('fuel-stations', ...)` (line 71) registers `GET fuel-stations/{fuel_station}` (show) before the custom single-segment routes `fuel-stations/search` (line 74) are registered. Laravel matches routes in registration order, so a GET to `/fuel-stations/search` matches the show route first (`{fuel_station}` = "search") and hits the empty `show()` stub (app/Http/Controllers/FuelStation/FulstationController.php:158-161) instead of `indexA()`. This route appears effectively unreachable as written. | 5 |
| app/Http/Controllers/FuelStation/FulstationController.php | 35 | `$total = $fuelStations->sum('amount')` sums only the current paginated page's collection, not the full filtered result set, but is presented to the user as "the total". | 4 |
| app/Repositories/FuelStation/EloquentFuelStation.php | 73-84 | `paginate()`'s Scout-search branch ignores `$start_date`/`$end_date` entirely (no date filter applied to the search query) even though the pagination links still `->appends(['start_date' => ..., 'end_date' => ...])`, implying the caller expects the filter to be honored. | 3 |
| app/Http/Controllers/FuelStation/FulstationController.php | 47-98 | `indexA()` instantiates `FuelStation::query()` directly, bypassing `FuelStationRepository` used everywhere else in the class — inconsistent with the repository pattern otherwise followed. | 3 |
| app/Http/Controllers/FuelStation/FulstationController.php | 76-90 | N+1 query risk: `$data->map(...)` accesses `$fuelStation->vehicle->name` per row (line 79) without `->with('vehicle')` eager-loading on the query built at line 50. | 4 |
| app/Http/Controllers/FuelStation/FulstationController.php | 225-242 | `status()` sets `$fuelStation->status = $request->status` directly from unvalidated request input (no `Validator`, no allowed-values check against `paid`/`unpaid`). | 3 |
| app/Http/Controllers/FuelStation/FulstationController.php | 244-259 | `updateStatus()` passes `$request->ids` straight into a bulk `whereIn(...)->update()` with no validation that `ids` is present/array/exists in `fuel_stations`. | 3 |
| app/Http/Controllers/FuelStation/FulstationController.php | 119-150, 175-206 | `store()` and `update()` duplicate an identical validation rule set verbatim (vehicle_id, name_owner, name_driver, name_distributor, filing_datetime, liter, amount, type_fuel) instead of sharing a `FormRequest`. | 3 |
| app/Http/Controllers/FuelStation/FulstationController.php | 137-149, 193-205, 210-222, 227-241, 246-258 | Identical `DB::beginTransaction()/commit()/rollBack()` + `toastr()` + `redirect()->back()` boilerplate repeated across 5 methods; each wraps a single Eloquent call, so the transaction adds no atomicity benefit and only duplicates control flow. | 2 |
| app/Http/Controllers/FuelStation/FulstationController.php | 113-116, 158-161, 169-172 | `create()`, `show()`, `edit()` are empty no-op stubs left over from the resource controller scaffold, yet are still routed (`Route::resource`) and reachable. | 2 |
| app/Models/FuelStation.php | 13 | `SoftCascadeTrait` is used on `FuelStation` itself but no `$softCascade` property is declared on this model (cascade is only configured on `Vehicle`, app/Models/Vehicle.php:21). The trait on `FuelStation` appears to have no effect (dead usage) unless a property is defined elsewhere not found in this scope. | 2 |
| app/Repositories/FuelStation/FuelStationRepository.php | 8, 35, 44 | Interface docblocks refer to "Coupon" (`@return Get all available Coupon`, `Paginate Coupons`) — copy-pasted from an unrelated repository, misleading documentation. | 1 |
| app/Models/FuelStation.php | 69 | Method name `getTotalLiterTypeDiesl()` is misspelled ("Diesl" instead of "Diesel"), inconsistent with `getTotalAmountTypeDiesel()` (line 84) which is spelled correctly. | 1 |
| app/Http/Controllers/FuelStation | class name | Controller class is `FulstationController` (missing the "e" — should be `FuelStationController`), propagated through the file name, namespace usage, and `routes/web.php:7,71-75`. Cosmetic but pervasive. | 1 |
| app/Http/Controllers/FuelStation/FulstationController.php | 24-26 | `index(Request $request)` declares `$request` as a parameter but reads filters via the `request()` helper instead (lines 26-29), only using the injected `$request` for `->ajax()` at line 34 — inconsistent style, no functional bug. | 1 |

No fat-controller-only-by-LOC concern beyond note: `FulstationController` is 260 LOC across 12 public methods (>150 LOC threshold), driven by duplicated try/catch/transaction blocks (see rows above) rather than a single oversized method.

## Open Questions

- Which Scout driver backs `FuelStation::search()` (app/Repositories/FuelStation/EloquentFuelStation.php:75) — Algolia, Meilisearch, database driver? Not present in the files read for this scope; would need `config/scout.php` to confirm, which is outside the assigned scope.
- Is there a `FuelStation` policy or gate anywhere in the app? None found under this scope's files or in the route middleware (`routes/web.php:71-75` only applies `auth`); cannot confirm authorization model without searching `app/Policies` and `AuthServiceProvider`, which are outside the assigned scope.
- Are `resources/views/content/Fuelstation/*.blade.php` (add, delete, edit, pagination-data, paided, test) themselves relying on business logic (e.g., computing totals/formatting in Blade)? Views were not read — out of the assigned scope (Controllers/FuelStation, Models/FuelStation.php, Repositories/FuelStation) — flagging as unverified rather than guessing.
- Confirmed via glob that `resources/views/content/Fuelstation/partials/actions.blade.php` does not exist anywhere in the project; not able to determine whether this view was deleted/renamed or never created, since view history/git log was not consulted (out of scope for a read-only static scan of the assigned files).

docs/audit/raw/02-fuel-station.md
