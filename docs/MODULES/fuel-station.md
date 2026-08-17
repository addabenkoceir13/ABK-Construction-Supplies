# Module: Fuel Station

Fuel receipts issued per vehicle. Each receipt records liters, amount, distributor, and fuel type, and moves
through an `unpaid` → `paid` status lifecycle. Depends on Vehicle & Fleet for the vehicle picklist and
`belongsTo` relation.

## Files

| File | LOC |
|---|---:|
| `app/Http/Controllers/FuelStation/FulstationController.php` | 260 |
| `app/Models/FuelStation.php` | 113 |
| `app/Repositories/FuelStation/FuelStationRepository.php` | 51 |
| `app/Repositories/FuelStation/EloquentFuelStation.php` | 136 |

Note: the controller's class name is `FulstationController` — missing the "e" in "Fuel" — propagated through
the filename, namespace, and 5 lines of `routes/web.php:7,71-75`.

## Responsibilities

### `FulstationController` (`app/Http/Controllers/FuelStation/FulstationController.php:13`)
Injects `FuelStationRepository`, `VehicleRepository` (`:18-22`).

| Method | Line | Behavior |
|---|---|---|
| `index()` | `:24` | Paginated unpaid receipts; JSON partial on AJAX, full view otherwise |
| `indexA()` | `:47` | DataTables server-side endpoint querying `FuelStation::query()` **directly** — bypasses the injected repository |
| `indexPaid()` | `:100` | Paid receipts |
| `create()`, `show()`, `edit()` | `:113`, `:158`, `:169` | Empty stubs, still routed |
| `store()` | `:119` | Validate → transaction → `FuelStationRepository::create()` |
| `update()` | `:175` | Same shape as `store()` |
| `destroy()` | `:208` | Soft-delete, transaction |
| `status()` | `:225` | Sets `$fuelStation->status = $request->status` **with no validation** of the value |
| `updateStatus()` | `:244` | Bulk `whereIn('id', $request->ids)->update(['status'=>'paid'])`, `$request->ids` unvalidated |

### `FuelStation` model (`app/Models/FuelStation.php:11`)
Traits: `HasFactory`, `SoftDeletes`, `SoftCascadeTrait`, `Laravel\Scout\Searchable` (`:13`) — note
`SoftCascadeTrait` is applied here with **no `$softCascade` property declared** (cascade is configured on
`Vehicle`, not `FuelStation` — the trait usage here appears to have no effect). `$fillable`: `vehicle_id,
name_owner, name_driver, name_distributor, filing_datetime, liter, amount, status, type_fuel` (`:15-25`).
Relation: `vehicle()` → `belongsTo(Vehicle::class)` (`:28-31`). `toSearchableArray()` (`:38-49`) indexes 7
fields for Scout.

11 static aggregate helpers, all thin `sum()`/`selectRaw` wrappers, no filtering logic:
`getTotalPaidFuel/getTotalUnPaidFuel/getTotalFuel/getTotalLiter/getTotalLiterTypeDiesl` (sic, misspelled —
contrast `getTotalAmountTypeDiesel` which is spelled correctly, `:69` vs `:84`) `/getTotalLiterGas
/getTotalLiterGasoline/getTotalAmountTypeDiesel/getTotalAmountGas/getTotalAmountGasoline/getMonthlyFuelData`
(`:51-112`). Consumed by `MODULES/dashboard-analytics.md`.

### Repository (`app/Repositories/FuelStation/{FuelStationRepository,EloquentFuelStation}.php`)
Interface docblocks reference "Coupon" — copy-pasted from an unrelated repository (`FuelStationRepository.php:8,35,44`).
`EloquentFuelStation::paginate($perPage, $search, $start_date, $end_date)` (`:71-106`) branches: if `$search`
is set, uses `FuelStation::search()` (Scout) — **and silently drops the `$start_date`/`$end_date` filters**
even though the pagination links still `->appends()` them. `paginatePaid()` (`:107-135`) is a plain Eloquent
query. **This is the one repository in the whole codebase whose `paginate()` genuinely paginates**
(`ARCHITECTURE.md` V10) — everything else in this module and the others returns a full `Collection` under a
`paginate()` name.

## Data flow

- `GET fuel-stations` → `index()` → `EloquentFuelStation::paginate()` → view `content.fuelstation.index`
  (non-AJAX) or JSON with rendered partial `content.Fuelstation.pagination-data` (AJAX).
- `GET fuel-stations/search` → **intended** to hit `indexA()`, but `routes/web.php:71` registers
  `Route::resource('fuel-stations', ...)` (which includes `GET fuel-stations/{fuel_station}`) *before*
  `fuel-stations/search` at `:74`. Laravel matches in registration order, so `/fuel-stations/search` matches
  `{fuel_station}` = `"search"` first and lands on the empty `show()` stub. **`indexA()` is unreachable as
  routed.**
- `GET fuel-stations/status/paid` → `indexPaid()` → `paginatePaid()` → view `content.Fuelstation.index`.
- `POST/PUT/DELETE fuel-stations[/...]` → validate → transaction → repository call → redirect with `toastr()` flash.
- `PATCH fuel-stations/status/{id}` and `POST fuel-stations/change-status` → unvalidated status writes (see below).
- Cross-module: `Vehicle::$softCascade = ['insuranceVehicle', 'fuelStations']` (`app/Models/Vehicle.php:21-25`)
  — soft-deleting a `Vehicle` cascades soft-delete into `FuelStation` rows via `askedio/laravel-soft-cascade`.

## Known issues (severity 4-5)

| Issue | Where | Severity |
|---|---|---|
| `view('content.fuelstation.index', ...)` — lowercase, tracked dir is `Fuelstation/` (capital F) | `FulstationController.php:44,97` | 5 |
| `view('content.Fuelstation.partials.actions', ...)` — directory doesn't exist at all | `FulstationController.php:88` | 5 |
| `fuel-stations/search` shadowed by resource `show` route, effectively dead | `routes/web.php:71` before `:74` | 5 |
| `$total = $fuelStations->sum('amount')` sums only the current page, presented to the user as "the total" | `FulstationController.php:35` | 4 |
| N+1: `$fuelStation->vehicle->name` accessed per row with no `->with('vehicle')` | `FulstationController.php:76-90` | 4 |
| `indexA()` bypasses the injected repository | `FulstationController.php:47-98` | 3 |
| Scout search branch drops date filters silently | `EloquentFuelStation.php:73-84` | 3 |
| `status()`/`updateStatus()` write unvalidated request input | `FulstationController.php:225-259` | 3 |

Full list with line numbers: `docs/audit/raw/02-fuel-station.md`.

## Open questions

- Which Scout driver actually backs `FuelStation::search()` in a real deployment — `config/scout.php:19`
  defaults to `algolia`, `.env.example:52` ships `database`. Unconfirmed which applies in production
  (`ARCHITECTURE.md` §7).
- Whether `resources/views/content/Fuelstation/partials/actions.blade.php` was deleted/renamed or never
  created — no git history was consulted.
