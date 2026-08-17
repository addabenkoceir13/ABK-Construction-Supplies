# Scope: Vehicle & Fleet

## Files
| File | LOC |
|---|---|
| app/Http/Controllers/Vehicle/VehicleController.php | 217 |
| app/Http/Controllers/TractorDriver/TractorDriverController.php | 116 |
| app/Models/Vehicle.php | 45 |
| app/Models/InsuranceVehicle.php | 43 |
| app/Models/TractorDriver.php | 26 |
| app/Repositories/Vehicle/VehicleRepository.php | 41 |
| app/Repositories/Vehicle/EloquentVehicle.php | 79 |
| app/Repositories/InsuranceVehicle/InsuranceVehicleRepository.php | 41 |
| app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php | 79 |
| app/Repositories/TractorDriver/TractorDriverRepository.php | 44 |
| app/Repositories/TractorDriver/EloquentTractorDriver.php | 88 |

Related views (not in assigned scope list, read for data-flow/business-logic checks only):
`resources/views/content/Vehicle/{index,create,edit,delete,added-date}.blade.php` (138/93/124/23/92 lines), `resources/views/content/TractorDriver/{index,create,edit,deleted}.blade.php` (138/54/55/23 lines).

Related migrations (read for schema confirmation only): `database/migrations/2024_11_03_090234_create_vehicles_table.php`, `database/migrations/2024_11_03_093212_create_insurance_vehicles_table.php`, `database/migrations/2024_09_19_094804_create_tractor_drivers_table.php`.

## Classes & Responsibilities

### `App\Http\Controllers\Vehicle\VehicleController` (app/Http/Controllers/Vehicle/VehicleController.php:13)
- Extends `App\Http\Controllers\Controller` (app/Http/Controllers/Vehicle/VehicleController.php:13).
- No traits.
- Constructor-injected: `VehicleRepository $vehicle`, `InsuranceVehicleRepository $insuranceVehicle` (app/Http/Controllers/Vehicle/VehicleController.php:18-22).
- Public methods:
  - `index()` (app/Http/Controllers/Vehicle/VehicleController.php:23-28) — fetches `vehicle->paginate(10)`, renders `content.Vehicle.index`.
  - `create()` (app/Http/Controllers/Vehicle/VehicleController.php:35-38) — empty stub.
  - `store(Request $request)` (app/Http/Controllers/Vehicle/VehicleController.php:40-82) — validates vehicle+insurance fields, builds `license_plate` string, creates a `Vehicle` then an `InsuranceVehicle` inside a DB transaction.
  - `show($id)` (app/Http/Controllers/Vehicle/VehicleController.php:85-88) — empty stub.
  - `edit(Request $request, $id)` (app/Http/Controllers/Vehicle/VehicleController.php:91-121) — validates `start_date`/`end_date`, creates a new `InsuranceVehicle` row inside a transaction. (See Smells: duplicates `addDateIns`, and is bound to a GET route.)
  - `update(Request $request, $id)` (app/Http/Controllers/Vehicle/VehicleController.php:123-164) — validates vehicle+insurance fields, rebuilds `license_plate`, updates `Vehicle` and updates `InsuranceVehicle` by `$request->insurance_id`.
  - `destroy($id)` (app/Http/Controllers/Vehicle/VehicleController.php:172-184) — deletes vehicle (soft delete via `SoftDeletes`+cascades via `SoftCascadeTrait`). No transaction opened; see Smells.
  - `addDateIns(Request $request, $id)` (app/Http/Controllers/Vehicle/VehicleController.php:186-216) — validates and creates a new `InsuranceVehicle` row for a vehicle inside a transaction. Logic is near-identical to `edit()`.

### `App\Http\Controllers\TractorDriver\TractorDriverController` (app/Http/Controllers/TractorDriver/TractorDriverController.php:12)
- Extends `App\Http\Controllers\Controller`.
- No traits.
- Constructor-injected: `TractorDriverRepository $tractorDriver` (app/Http/Controllers/TractorDriver/TractorDriverController.php:16-19). Also imports (but never uses) `App\Repositories\Supplier\SupplierRepository` (app/Http/Controllers/TractorDriver/TractorDriverController.php:6).
- Public methods:
  - `index()` (app/Http/Controllers/TractorDriver/TractorDriverController.php:21-25) — `tractorDriver->all()` (no pagination), renders `content.TractorDriver.index`.
  - `create()` (app/Http/Controllers/TractorDriver/TractorDriverController.php:27-30) — empty stub.
  - `store(Request $request)` (app/Http/Controllers/TractorDriver/TractorDriverController.php:32-57) — validates `fullname`, `phone`; passes `$request->all()` straight to repository `create()` inside a transaction.
  - `show($id)` (app/Http/Controllers/TractorDriver/TractorDriverController.php:60-63) — empty stub.
  - `edit($id)` (app/Http/Controllers/TractorDriver/TractorDriverController.php:65-68) — empty stub.
  - `update(Request $request, $id)` (app/Http/Controllers/TractorDriver/TractorDriverController.php:71-96) — validates `fullname`, `phone`; passes `$request->all()` to repository `update()` inside a transaction.
  - `destroy($id)` (app/Http/Controllers/TractorDriver/TractorDriverController.php:99-115) — deletes via repository inside a transaction.

### `App\Models\Vehicle` (app/Models/Vehicle.php:11)
- Traits: `HasFactory`, `SoftDeletes`, `Askedio\SoftCascade\Traits\SoftCascadeTrait` (app/Models/Vehicle.php:13).
- `$fillable`: `name`, `type`, `license_plate` (app/Models/Vehicle.php:15-19).
- `$softCascade`: `['insuranceVehicle', 'fuelStations']` (app/Models/Vehicle.php:21) — soft-deleting a Vehicle cascades soft-delete to its insurance rows and fuel-station rows.
- Relations:
  - `fuelStations()`: `hasMany(FuelStation::class)` (app/Models/Vehicle.php:23-26).
  - `insuranceVehicle()`: `hasMany(InsuranceVehicle::class)->orderBy('end_date', 'desc')` (app/Models/Vehicle.php:28-31).
- Public method `insuranceDateExpiredLast(): bool` (app/Models/Vehicle.php:33-44) — re-queries `InsuranceVehicle::where('vehicle_id', $this->id)->orderBy('end_date','desc')->first()` instead of using the already-declared `insuranceVehicle()` relation; compares latest `end_date` to `Carbon::today()`.

### `App\Models\InsuranceVehicle` (app/Models/InsuranceVehicle.php:11)
- Traits: `HasFactory`, `SoftDeletes`, `SoftCascadeTrait` (app/Models/InsuranceVehicle.php:13).
- `$fillable`: `vehicle_id`, `start_date`, `end_date` (app/Models/InsuranceVehicle.php:15-19).
- Relation: `vehicle()`: `belongsTo(Vehicle::class)` (app/Models/InsuranceVehicle.php:21-24).
- `insuranceDateExpired(): bool` (app/Models/InsuranceVehicle.php:26-29) — compares this row's `end_date` to today.
- `insuranceDateExpiredLast(): bool` (app/Models/InsuranceVehicle.php:30-41) — `self::orderBy('end_date','desc')->first()` **without a `vehicle_id` scope**, i.e. it finds the latest `end_date` across ALL vehicles' insurance rows, not just the current model's vehicle. Near-duplicate of `Vehicle::insuranceDateExpiredLast()`.

### `App\Models\TractorDriver` (app/Models/TractorDriver.php:10)
- Traits: `HasFactory`, `SoftDeletes`, `SoftCascadeTrait` (app/Models/TractorDriver.php:12). No `$softCascade` property declared despite using the trait.
- `$fillable`: `fullname`, `phone`, `type`, `status` (app/Models/TractorDriver.php:14-19).
- Relation: `debts()`: `hasMany(Debt::class)` (app/Models/TractorDriver.php:22-25).

### Repositories (Vehicle / InsuranceVehicle / TractorDriver)
All three follow the same interface+Eloquent-impl pattern, bound in `app/Providers/EloquentRepositoryProvider.php:39-41`:
- `VehicleRepository` (app/Repositories/Vehicle/VehicleRepository.php) / `EloquentVehicle` (app/Repositories/Vehicle/EloquentVehicle.php): `all()`, `find($id)`, `create(array $data)`, `update($id, array $data)`, `delete($id)`, `paginate($perPage, $search = null)`.
- `InsuranceVehicleRepository` / `EloquentInsuranceVehicle`: same shape as above (app/Repositories/InsuranceVehicle/InsuranceVehicleRepository.php, app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php).
- `TractorDriverRepository` / `EloquentTractorDriver`: same shape plus `TractorDriverNormal()` (app/Repositories/TractorDriver/TractorDriverRepository.php:13, app/Repositories/TractorDriver/EloquentTractorDriver.php:21-24 — `TractorDriver::whereType('normal')->first()`) and `TractorDriverDeliveryActive()` (app/Repositories/TractorDriver/TractorDriverRepository.php:15, app/Repositories/TractorDriver/EloquentTractorDriver.php:25-28 — `TractorDriver::whereStatus('active')->whereType('delivery')->get()`). These two are consumed outside this scope (see External Dependencies).
- In every one of the three `paginate($perPage, $search)` implementations, `$perPage` is accepted but never applied — the method runs `Model::query()->orderBy('id','desc')->get()` and returns a plain `Collection`, not a `LengthAwarePaginator` (app/Repositories/Vehicle/EloquentVehicle.php:66-77, app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php:66-77, app/Repositories/TractorDriver/EloquentTractorDriver.php:75-86). `$search` is also unused for filtering (only `->appends()`, which is a paginator-only method invalid on a Collection — dead/broken branch, never hit because `$search` is never passed by callers in this scope).

## Data Flow (entrypoint -> exit)

**Create vehicle:** `POST services/vehicle` (routes/web.php:56) → `VehicleController::store` (app/Http/Controllers/Vehicle/VehicleController.php:40) → validates → `VehicleRepository::create()` → `EloquentVehicle::create()` → `Vehicle::create()` (app/Repositories/Vehicle/EloquentVehicle.php:32-37) → then `InsuranceVehicleRepository::create()` → `EloquentInsuranceVehicle::create()` → `InsuranceVehicle::create()` (app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php:32-37) → both wrapped in one `DB::beginTransaction()/commit()` → `redirect()->back()` with `toastr()` flash.

**List vehicles:** `GET services/vehicle` (routes/web.php:56) → `VehicleController::index` → `VehicleRepository::paginate(10)` (actually returns full `Collection`, unsorted-by-page — app/Repositories/Vehicle/EloquentVehicle.php:66-77) → view `content.Vehicle.index` (resources/views/content/Vehicle/index.blade.php) → for each row calls `$vehicle->insuranceDateExpiredLast()` (app/Models/Vehicle.php:33) which fires a fresh `InsuranceVehicle::where(...)` query per row (resources/views/content/Vehicle/index.blade.php:58).

**Add insurance date:** `POST services/vehicle/{vehicle}/added-date` (routes/web.php:57) → `VehicleController::addDateIns` (app/Http/Controllers/Vehicle/VehicleController.php:186) → validates → `InsuranceVehicleRepository::create()` inside a transaction → redirect back.

**Update vehicle:** `PUT|PATCH services/vehicle/{vehicle}` → `VehicleController::update` (app/Http/Controllers/Vehicle/VehicleController.php:123) → validates → updates `Vehicle` row and (if `insurance_id` present in the request) updates the `InsuranceVehicle` row → transaction → redirect back.

**Delete vehicle:** `DELETE services/vehicle/{vehicle}` → `VehicleController::destroy` (app/Http/Controllers/Vehicle/VehicleController.php:172) → `VehicleRepository::delete()` → `Vehicle::delete()` (soft delete) → `SoftCascadeTrait` cascades soft-delete to `insuranceVehicle` and `fuelStations` relations (app/Models/Vehicle.php:21) → redirect back. No transaction wraps this multi-table cascade.

**Tractor driver CRUD:** `services/tractor-driver` resource routes (routes/web.php:55) → `TractorDriverController` → `TractorDriverRepository` → `TractorDriver` model. `index()` fetches ALL drivers (`->all()`, app/Repositories/TractorDriver/EloquentTractorDriver.php:16-19) into `content.TractorDriver.index` (resources/views/content/TractorDriver/index.blade.php), no pagination.

**Consumption outside scope:**
- `App\Models\FuelStation::vehicle()` → `belongsTo(Vehicle::class)` (app/Models/FuelStation.php:30); `FulstationController` injects `VehicleRepository` (app/Http/Controllers/FuelStation/FulstationController.php:8,18) and reads `$fuelStation->vehicle->name` (app/Http/Controllers/FuelStation/FulstationController.php:79).
- `App\Models\Debt::tractorDriver()`-style relation → `belongsTo(TractorDriver::class, 'tractor_driver_id')` (app/Models/Debt.php:37).
- `DebtController` and `DebtWithSupplierController` both inject `TractorDriverRepository` and call `TractorDriverNormal()` / `TractorDriverDeliveryActive()` (app/Http/Controllers/Debt/DebtController.php:11,27,43,55; app/Http/Controllers/Debt/DebtWithSupplierController.php:10,27,44,57) — i.e. the Debt module depends on TractorDriver repository methods that are otherwise undocumented/untested within this scope.
- `routes/web.php:96` queries `Debt::whereStatus('unpaid')->where('tractor_driver_id','!=',1)->...` directly (outside this scope's classes, flagged only as evidence tractor_driver_id=1 is a "magic" sentinel row used elsewhere).

## External Dependencies (packages, APIs, queues)
- `Askedio\SoftCascade\Traits\SoftCascadeTrait` (composer package `askedio/laravel-soft-cascade`) — used by `Vehicle`, `InsuranceVehicle`, `TractorDriver` models (app/Models/Vehicle.php:5,13; app/Models/InsuranceVehicle.php:5,13; app/Models/TractorDriver.php:6,12) to cascade soft-deletes.
- `Carbon\Carbon` — date comparisons in `Vehicle::insuranceDateExpiredLast()` and `InsuranceVehicle::insuranceDateExpired()/insuranceDateExpiredLast()` (app/Models/Vehicle.php:6,43; app/Models/InsuranceVehicle.php:6,28,40).
- `toastr()` helper (likely `yoeunes/toastr` or similar) — flash-message UX in both controllers, every branch (e.g. app/Http/Controllers/Vehicle/VehicleController.php:52,74,79,98,113,118 etc.).
- `Illuminate\Support\Facades\DB` — manual transaction control (`beginTransaction`/`commit`/`rollBack`) in nearly every write method of both controllers.
- `Illuminate\Support\Facades\Validator` — inline validation facade, used instead of `FormRequest` classes; none exist for Vehicle/TractorDriver (confirmed no matches under `app/Http/Requests` for `*Vehicle*` or `*Tractor*`).
- No queues, jobs, listeners, events, observers, policies, or external APIs were found wired to any class in this scope.

## Smells & Debt

| File | Line | Issue | Severity |
|---|---|---|---|
| app/Http/Controllers/Vehicle/VehicleController.php | 172-184 | `destroy()` catch block calls `DB::rollBack()` but no `DB::beginTransaction()` was ever opened in this method — will raise "There is no active transaction" if `$this->vehicle->delete($id)` throws, masking the real error. | 4 |
| resources/views/content/Vehicle/index.blade.php | 58 | `$vehicle->insuranceDateExpiredLast()` is called inside the `@foreach` loop; it runs a brand-new `InsuranceVehicle::where('vehicle_id', ...)` query per row instead of reusing the eager-loadable `insuranceVehicle()` relation — classic N+1 (app/Models/Vehicle.php:33-44 defines the offending method). | 4 |
| app/Repositories/Vehicle/EloquentVehicle.php | 66-77 | `paginate($perPage, $search)` ignores `$perPage` entirely and returns `->get()` (a `Collection`), not a `LengthAwarePaginator`; combined with `VehicleController::index()` calling it as `paginate(10)` (app/Http/Controllers/Vehicle/VehicleController.php:25), every vehicle row (and its N+1 insurance check) loads on every request regardless of page size — misleading name, no real pagination. | 3 |
| app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php | 66-77 | Same broken `paginate()` pattern, currently unused by any controller in scope — dead/misleading method. | 2 |
| app/Repositories/TractorDriver/EloquentTractorDriver.php | 75-86 | Same broken `paginate()` pattern; `TractorDriverController::index()` uses `->all()` instead (app/Http/Controllers/TractorDriver/TractorDriverController.php:23), so this method is dead code. | 2 |
| app/Http/Controllers/Vehicle/VehicleController.php | 91-121 vs 186-216 | `edit(Request $request, $id)` and `addDateIns(Request $request, $id)` contain near-identical logic (validate start/end date, create an `InsuranceVehicle` row, commit/rollback, flash message) — duplicated business logic. `edit()` is additionally routed as a resource `GET` endpoint (routes/web.php:56, `services.vehicle.edit`) yet performs a database write, violating HTTP GET safety semantics; no Blade view in the scope posts to `services.vehicle.edit`, so this write path appears unreachable through the normal UI (confirmed no matches searching views for `services.vehicle.edit`). | 3 |
| app/Models/Vehicle.php | 33-44 | `Vehicle::insuranceDateExpiredLast()` duplicates `InsuranceVehicle::insuranceDateExpiredLast()` (app/Models/InsuranceVehicle.php:30-41) almost line-for-line; the `Vehicle` version also bypasses its own declared `insuranceVehicle()` relation (app/Models/Vehicle.php:28-31) in favor of a raw `InsuranceVehicle::where(...)` query. | 2 |
| app/Models/InsuranceVehicle.php | 30-41 | `insuranceDateExpiredLast()` on an `InsuranceVehicle` instance queries `self::orderBy('end_date','desc')->first()` with **no `vehicle_id` filter** — it returns the latest insurance end-date across every vehicle in the table, not just this record's vehicle. Method is unused within this scope's controllers/views (only `Vehicle::insuranceDateExpiredLast()` is called from the index view), but is public API and could be misused with incorrect results. | 3 |
| app/Http/Controllers/TractorDriver/TractorDriverController.php | 6, 46, 85 | Imports `App\Repositories\Supplier\SupplierRepository` but never uses it (dead import); local variables holding the created/updated `TractorDriver` are named `$supplier` (lines 46 and 85) — strong evidence this controller was copy-pasted from a Supplier controller without full cleanup. | 2 |
| app/Repositories/TractorDriver/EloquentTractorDriver.php | 5, 7 | Imports `App\Models\Supplier` and `App\Repositories\Supplier\SupplierRepository`, neither used anywhere in the file — dead imports, same copy-paste origin as above. | 1 |
| app/Repositories/InsuranceVehicle/EloquentInsuranceVehicle.php | 44-48 | `update()` assigns the found record to `$Vehicle` (not `$InsuranceVehicle`) — cosmetic but confirms the file was cloned from `EloquentVehicle.php` without renaming locals. | 1 |
| app/Http/Controllers/TractorDriver/TractorDriverController.php | 32-57, 71-96 | `store()`/`update()` validate only `fullname` and `phone`, then forward the entire `$request->all()` to `TractorDriverRepository::create()/update()`. Because `TractorDriver::$fillable` also includes `type` and `status` (app/Models/TractorDriver.php:14-19), any caller can mass-assign those enum columns with unvalidated values via the same form submission. | 3 |
| app/Http/Controllers/Vehicle/VehicleController.php, app/Http/Controllers/TractorDriver/TractorDriverController.php | throughout | No `FormRequest` classes exist for either controller (validation confirmed absent from `app/Http/Requests`); the same `Validator::make([...])` rule sets and try/catch/`DB::beginTransaction()` boilerplate are duplicated across `store()`/`update()`/`edit()`/`addDateIns()` in both controllers instead of being extracted. | 2 |
| app/Http/Controllers/Vehicle/VehicleController.php | 6 | `use App\Models\InsuranceVehicle;` imported but the class name is never referenced in the file body — repository pattern is otherwise followed consistently, this import is leftover/dead. | 1 |
| app/Models/TractorDriver.php | 12 | Uses `SoftCascadeTrait` but declares no `$softCascade` property (contrast with `Vehicle`/`InsuranceVehicle`, which do) — likely harmless (trait no-ops with nothing configured) but inconsistent with sibling models in this scope and worth confirming intent. | 1 |

## Open Questions
- Is `VehicleController::edit()` (app/Http/Controllers/Vehicle/VehicleController.php:91-121) genuinely dead code, or is it invoked by some client-side JS/AJAX not present in the Blade files read (`resources/views/content/Vehicle/*.blade.php`)? Only static route/view analysis was performed; no JS asset files were inspected for AJAX calls to `services.vehicle.edit`.
- `TractorDriver::debts()` relation cardinality/usage and the `tractor_driver_id != 1` sentinel value seen in routes/web.php:96 are outside this scope's file list (`app/Models/Debt.php`, `routes/web.php` business logic) — flagged for the Debt-module audit rather than analyzed here.
- Whether `EloquentVehicle::paginate()`, `EloquentInsuranceVehicle::paginate()`, and `EloquentTractorDriver::paginate()` are called from anywhere outside this scope (e.g. an API layer not in the assigned file list) was not verified beyond `grep` over `app/`; if unused elsewhere too, they are candidates for removal.
- No test files (unit/feature) for Vehicle, InsuranceVehicle, or TractorDriver were located during this audit — test coverage was not in the assigned scope's file list, so this is noted rather than confirmed exhaustively.
