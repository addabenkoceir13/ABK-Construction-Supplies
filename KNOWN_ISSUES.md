# Known Issues

Discovered while building the characterization/benchmark safety net for
`App\Http\Controllers\Debt\DebtController` and
`App\Http\Controllers\Debt\DebtWithSupplierController` (see `/perf-audit`,
`/perf-plan`, `/perf-safety-net`). None of these are fixed here — the safety
net locks in current behavior, bugs included, so the performance refactor
waves can be verified as behavior-preserving.

## 1. `tractor_driver_id = 1` is a hardcoded magic number for "walk-in customer"

`EloquentDebt::debtPaid()` / `debtUnPaid()` / `getSupplier()`
(`app/Repositories/Debt/EloquentDebt.php`) filter with
`->whereTractorDriverId(1)` / `->where('tractor_driver_id', '!=', '1')`
instead of resolving the "normal" driver dynamically via
`TractorDriverNormal()` (`whereType('normal')->first()`), which the same
controller already calls elsewhere. If the "normal" tractor driver row is
ever anything other than id `1` (e.g. after a reseed, a manual delete/recreate,
or a production data import), `/debt` and `/debt/status/paid` will silently
show zero rows or the wrong driver's debts.

## 2. `index()` / `indexPaid()` have no pagination, filtering, or sorting

**RESOLVED in `/perf-wave-b`** (`debtPaid()`/`debtUnPaid()` now `paginate(25)`).
Originally: `->get()` with no `limit`/`paginate`, every matching row loaded
and rendered in a single response — 1701 rows on `/debt/status/paid` in
production. Left here for the audit trail; characterization tests were
updated alongside the fix (see the Wave B commit).

## 3. `show()` / `edit()` return a 500, not a 404, for a nonexistent debt

`DebtController::show()` and `edit()` call `$this->debt->find($id)`
(`Debt::find($id)`) with no `findOrFail()` / existence check. When `$id`
doesn't match any row, `$debt` is `null` and the Blade view immediately
throws `Error: Attempt to read property "fullname" on null`, which surfaces
as an HTTP 500 instead of a 404. Characterized in
`test_show_with_a_nonexistent_debt_id_currently_errors_instead_of_404`.

## 4. No authorization/ownership check on any Debt action

There is no `DebtPolicy` and no `$policies` entry in `AuthServiceProvider`.
Every `DebtController` route only requires the `auth` middleware — any
authenticated user can view, edit, delete, or record a payment against
*any* debt, regardless of who created it or their role. Characterized in
`test_index_is_accessible_to_any_authenticated_user_no_ownership_or_role_check`.

## 5. `App\Models\SubCategory` is referenced elsewhere as `Subcategory` (case mismatch)

`app/Models/SubCategory.php` declares `class SubCategory`, but
`DebtProduct::getSubcategory()` (`app/Models/DebtProduct.php:31`) references
it as `Subcategory::class` (lowercase `c`) with no explicit `use` import, so
it resolves via the shared `App\Models` namespace to `App\Models\Subcategory`.
This works today because PHP class-name resolution is case-insensitive *and*
this environment's filesystem (Windows) is case-insensitive, so the
autoloader finds `SubCategory.php` despite the case mismatch. On a
case-sensitive filesystem (typical Linux production host) this would fail to
autoload. Not touched here since it currently passes in this environment and
is outside the scope of the Debt performance work — worth a dedicated fix
separately.

## 6. `update()`'s catch block calls `dd()` before its `toastr()`/redirect

`DebtController::update()`'s `catch (\Exception $e)` block calls
`dd($e->getMessage())` immediately before `toastr()->error($e->getMessage());
return redirect()->back();`. Since `dd()` dumps and terminates the request,
those two lines are unreachable — any exception during an update currently
dumps a var-dump page to the user instead of the intended graceful toastr
error + redirect. Preserved byte-for-byte during the Wave C refactor (see
`/perf-wave-c`); trivial one-line fix (delete the `dd()` call) once wanted.

## 7. `DebtWithSupplierController::show()` 500s on every view — nonexistent relation

**RESOLVED in `/perf-wave-a`** (`view.blade.php:20` now reads
`$debt->tractorDriver->fullname`, eager-loaded via `loadMissing()` in the
controller). Originally: read `$debt->getSupplier->fullname`, a relation
that doesn't exist on `Debt` (only `tractorDriver()`). Initial investigation
via `php artisan tinker` suggested this only produced a silent PHP warning
(blank field, page still 200s) — that was **wrong**: a real HTTP request
goes through Laravel's `HandleExceptions` bootstrap, which converts the
warning into an `ErrorException`, so this 500'd on every single view before
the fix (confirmed by the characterization test before it was updated).

## 8. `DebtWithSupplierController::driverDebtPaid()`/`driverDebtUnPaid()` are unbounded

**RESOLVED in `/perf-wave-b`** (`paginate(25)`, same as issue #2). Left here
for the audit trail; originally `->get()` with no limit — 1188 rows on
`/debt-supplier/status/paid` in production.

## 9. `DebtWithSupplierController::destroy()` redirects to the wrong index

**RESOLVED in `/perf-wave-a`** (`route('debt-supplier.index')`). Originally
`return redirect()->route('debt.index');` — `DebtController`'s index route,
not this controller's own, almost certainly a copy-paste leftover.

## 10. `DebtWithSupplierController::update()` has the same unreachable `dd()` as issue #6

`app/Http/Controllers/Debt/DebtWithSupplierController.php:275` — identical
bug to issue #6 (`dd($e->getMessage())` before the `toastr()`/redirect in the
catch block, making those lines unreachable). Not part of the two items you
approved fixing (#7/blank-field-turned-500 and the wrong redirect target);
documented-only, consistent with how issue #6 was handled for the sibling
controller.

## 11. `DebtWithSupplierController::payDebt()` also redirects to the wrong index

Discovered during `/perf-wave-c` while extracting the payment logic into the
shared `DebtPaymentCalculator`. The "amount paid exceeds the amount owed"
branch (`app/Http/Controllers/Debt/DebtWithSupplierController.php`, in
`payDebt()`) does `return redirect()->route('debt.index');` — the same
wrong-controller mistake as issue #9 had in `destroy()`, but this one
wasn't caught by `/perf-audit` and wasn't part of the two fixes you
approved (issue #7 and issue #9). Preserved byte-for-byte during the Wave C
extraction (verified with a throwaway test asserting the exact wrong
redirect target); trivial one-line fix (`route('debt.index')` →
`route('debt-supplier.index')`) once wanted.

## 12. Pre-existing failing test: `tests/Feature/ExampleTest.php`

`test_the_application_returns_a_successful_response` asserts `GET /` returns
`200`, but `routes/web.php:47` has since added `->middleware('auth')` to that
route, so an unauthenticated request now 302-redirects to `login`. This
failure predates this session's changes (confirmed via `git diff` — the file
is untouched) and is unrelated to `DebtController`; not fixed here as it's
outside the scope of this performance work.
