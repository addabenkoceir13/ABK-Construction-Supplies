# Module: Vehicle & Fleet

Vehicle registry, insurance-expiry tracking, and the tractor-driver registry (which also doubles as the
client/supplier discriminator for Debt — see `DOMAIN-MODEL.md`).

## Files

| File | LOC |
|---|---:|
| `app/Http/Controllers/Vehicle/VehicleController.php` | 217 |
| `app/Http/Controllers/TractorDriver/TractorDriverController.php` | 116 |
| `app/Models/Vehicle.php` | 45 |
| `app/Models/InsuranceVehicle.php` | 43 |
| `app/Models/TractorDriver.php` | 26 |
| `app/Repositories/Vehicle/{VehicleRepository,EloquentVehicle}.php` | 41 / 79 |
| `app/Repositories/InsuranceVehicle/{InsuranceVehicleRepository,EloquentInsuranceVehicle}.php` | 41 / 79 |
| `app/Repositories/TractorDriver/{TractorDriverRepository,EloquentTractorDriver}.php` | 44 / 88 |

## Responsibilities

### `VehicleController` (`app/Http/Controllers/Vehicle/VehicleController.php:13`)
Injects `VehicleRepository`, `InsuranceVehicleRepository` (`:18-22`).

| Method | Line | Behavior |
|---|---|---|
| `index()` | `:23-28` | `vehicle->paginate(10)` (does not actually paginate, see below) |
| `store()` | `:40-82` | Validates vehicle+insurance fields, builds `license_plate` as `"{license} - {year} - {wilaya}"`, creates `Vehicle` then `InsuranceVehicle` in one transaction |
| `edit()` | `:91-121` | **GET route that writes to the DB** — creates a new `InsuranceVehicle` row. Near-duplicate of `addDateIns()`. No Blade view posts to this route, so it appears unreachable through the normal UI, but violates GET safety semantics if ever invoked |
| `update()` | `:123-164` | Rebuilds `license_plate`, updates `Vehicle` and (if `insurance_id` present) the matching `InsuranceVehicle` row |
| `destroy()` | `:172-184` | Soft-deletes (cascades via `SoftCascadeTrait`); catch block calls `rollBack()` with **no matching `beginTransaction()`** |
| `addDateIns()` | `:186-216` | Adds a new `InsuranceVehicle` row for a vehicle; logic near-identical to `edit()` |

### `TractorDriverController` (`app/Http/Controllers/TractorDriver/TractorDriverController.php:12`)
Injects `TractorDriverRepository` (`:16-19`). Also imports (but never uses) `SupplierRepository` (`:6`) — a
dead import from an interface that has no implementation anywhere in the codebase.

| Method | Line | Behavior |
|---|---|---|
| `index()` | `:21-25` | `tractorDriver->all()` — **no pagination at all**, loads every row |
| `store()` | `:32-57` | Validates only `fullname`, `phone`; passes `$request->all()` straight to the repository, mass-assigning `type` and `status` unvalidated |
| `update()` | `:71-96` | Same validation gap as `store()` |
| `destroy()` | `:99-115` | Transactional delete |

Local variables holding the created/updated driver are named `$supplier` at `:46,85` — evidence this
controller was copy-pasted from an (unfinished) Supplier controller without full cleanup.

### `Vehicle` model (`app/Models/Vehicle.php:11`)
`$softCascade = ['insuranceVehicle', 'fuelStations']` (`:21`) — the sole delete-integrity declaration for the
fleet subtree (`ARCHITECTURE.md` Wall 5). Relations: `fuelStations()` → `hasMany(FuelStation)` (`:23-26`),
`insuranceVehicle()` → `hasMany(InsuranceVehicle)->orderBy('end_date','desc')` (`:28-31`).
`insuranceDateExpiredLast(): bool` (`:33-44`) **bypasses its own declared relation** and re-queries
`InsuranceVehicle::where('vehicle_id', $this->id)->orderBy('end_date','desc')->first()` fresh — called once
per row from `resources/views/content/Vehicle/index.blade.php:58` inside a `@foreach`, guaranteeing an N+1.

### `InsuranceVehicle` model (`app/Models/InsuranceVehicle.php:11`)
Relation: `vehicle()` → `belongsTo(Vehicle)` (`:21-24`). `insuranceDateExpired()` (`:26-29`) is correctly
row-scoped. `insuranceDateExpiredLast()` (`:30-41`) is **not** — `self::orderBy('end_date','desc')->first()`
with no `vehicle_id` filter returns the latest insurance expiry across the *entire* `insurance_vehicles`
table, not this vehicle's. Unused within the current UI (only `Vehicle::insuranceDateExpiredLast()` is
called), but public API and a landmine if wired up elsewhere.

### `TractorDriver` model (`app/Models/TractorDriver.php:10`)
`$fillable = ['fullname','phone','type','status']`. Relation: `debts()` → `hasMany(Debt)` (`:22-25`). Uses
`SoftCascadeTrait` with **no `$softCascade` property declared** — inconsistent with `Vehicle`/`InsuranceVehicle`
in the same scope, which do declare one.

## Repository layer

All three repository pairs (`Vehicle`, `InsuranceVehicle`, `TractorDriver`) share one bug: `paginate($perPage,
$search)` **ignores `$perPage` entirely** and returns `Model::query()->orderBy('id','desc')->get()` — a
`Collection`, not a `LengthAwarePaginator` (`app/Repositories/Vehicle/EloquentVehicle.php:66-77` and the two
siblings). `VehicleController::index()` calls this as `paginate(10)` believing it gets 10 rows; it gets every
row, including the N+1 insurance check on each. `TractorDriverController::index()` sidesteps the bug by not
calling `paginate()` at all — it calls `->all()` instead, which is its own problem (no pagination whatsoever).

## Data flow

**Create vehicle:** `POST services/vehicle` → validate → `VehicleRepository::create()` → `InsuranceVehicleRepository::create()`
→ both in one transaction → redirect.

**List vehicles:** `GET services/vehicle` → `paginate(10)` (returns full Collection) → view
`content.Vehicle.index` → per row, `$vehicle->insuranceDateExpiredLast()` fires a fresh query.

**Delete vehicle:** `DELETE services/vehicle/{vehicle}` → soft-delete → `SoftCascadeTrait` cascades to
`insuranceVehicle` and `fuelStations` relations — **this multi-table cascade is not wrapped in a transaction**,
so a partial cascade is a live possibility if any step fails.

**Cross-module:** `FuelStation::vehicle()` belongs-to `Vehicle`; `FulstationController` injects
`VehicleRepository` for the picklist. `Debt::tractorDriver()` belongs-to `TractorDriver`; both Debt
controllers inject `TractorDriverRepository` and call `TractorDriverNormal()`/`TractorDriverDeliveryActive()`.

## Known issues (severity 3-4)

| Issue | Where | Severity |
|---|---|---|
| `destroy()` rollback with no matching `beginTransaction()` | `VehicleController.php:172-184` | 4 |
| N+1: `insuranceDateExpiredLast()` called per row in Blade `@foreach` | `Vehicle/index.blade.php:58` | 4 |
| `paginate()` ignores `$perPage`, returns full `Collection` | all three `Eloquent*` repos | 3 |
| `edit()` performs a DB write on a GET-routed resource method, duplicating `addDateIns()` | `VehicleController.php:91-121` vs `:186-216` | 3 |
| `store()`/`update()` mass-assign `type`/`status` on `TractorDriver` with no validation | `TractorDriverController.php:32-57,71-96` | 3 |
| `InsuranceVehicle::insuranceDateExpiredLast()` has no `vehicle_id` scope | `InsuranceVehicle.php:30-41` | 3 |

Full list with line numbers: `docs/audit/raw/03-vehicle-fleet.md`.

## Open questions

- Is `VehicleController::edit()` genuinely dead code, or invoked by JS/AJAX not present in the audited Blade
  files? Not resolvable from static route/view analysis alone.
- Are `EloquentVehicle::paginate()`, `EloquentInsuranceVehicle::paginate()`, `EloquentTractorDriver::paginate()`
  called from anywhere outside this scope? If not, they're candidates for removal.
