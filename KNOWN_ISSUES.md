# Known Issues

Discovered while building the characterization/benchmark safety net for
`App\Http\Controllers\Debt\DebtController` (see `/perf-audit`, `/perf-plan`,
`/perf-safety-net`). None of these are fixed here — the safety net locks in
current behavior, bugs included, so the performance refactor waves can be
verified as behavior-preserving.

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

Confirmed by `/perf-audit`: `debtPaid()`/`debtUnPaid()` call `->get()` with no
`limit`/`paginate`. Every matching row is loaded and rendered in a single
response — currently 1701 rows on `/debt/status/paid` in production. This is
the subject of the approved Wave A fix; recorded here too since the
characterization tests in `tests/Feature/Debt/DebtControllerTest.php`
explicitly lock in the *current* no-pagination behavior (e.g.
`test_index_ignores_the_page_query_param_because_pagination_does_not_exist_yet`)
and will need updating once Wave A adds real pagination.

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

## 6. Pre-existing failing test: `tests/Feature/ExampleTest.php`

`test_the_application_returns_a_successful_response` asserts `GET /` returns
`200`, but `routes/web.php:47` has since added `->middleware('auth')` to that
route, so an unauthenticated request now 302-redirects to `login`. This
failure predates this session's changes (confirmed via `git diff` — the file
is untouched) and is unrelated to `DebtController`; not fixed here as it's
outside the scope of this performance work.
