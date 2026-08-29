---
description: Phase 3 — Write characterization + benchmark tests that lock in current behavior
argument-hint: <route name | Controller@method>
allowed-tools: Read, Write, Edit, Grep, Glob, Bash(php artisan test:*), Bash(./vendor/bin/pest:*), Bash(./vendor/bin/phpunit:*), Bash(php artisan make:test:*)
---

Build a behavior-preservation safety net for: $ARGUMENTS

Before writing anything, detect whether this project uses **Pest** or **PHPUnit** and follow the existing test conventions exactly.

## 1. Characterization tests
Write tests that assert **current** behavior, including behavior that looks wrong.

Cover:
- Empty state (no records)
- Single record
- Large-ish dataset (enough rows to exercise pagination)
- Each filter, search param, and sort option the page accepts
- Pagination: page 1, last page, out-of-range page
- Authorization: guest, authenticated-unauthorized, authenticated-authorized

Because this is a Blade app, assert on the rendered response, not on JSON:
- `$response->assertOk()`
- `$response->assertViewIs('...')`
- `$response->assertViewHas('key', fn ($v) => ...)` — assert the shape and count of what reaches the view
- `assertSeeText()` / `assertDontSeeText()` for the key rows
- For paginated data, assert the paginator's `total()`, `perPage()`, and `count()`

Seed with factories and a **fixed seed** so the dataset is deterministic.

## 2. Benchmark test
A separate test that records and asserts the current cost:
- Query count — use `assertDatabaseQueryCount()` if available, otherwise wrap with `DB::enableQueryLog()` / `count(DB::getQueryLog())`
- `memory_get_peak_usage(true)`
- Wall time as a rough guard only (loose bound, it is machine-dependent)

Write today's real numbers into the test as the **baseline**, with a comment marking them as the pre-refactor values.

## 3. Rules
- **Do not fix any bug you find.** Record it in `KNOWN_ISSUES.md` at the project root and move on.
- Do not touch application code. Tests and `KNOWN_ISSUES.md` only.
- Use `RefreshDatabase` (or the project's existing trait) — never point tests at the dev database.

## Output
Run the suite, show me it passing, and print the baseline table:

| Metric | Baseline |
|--------|----------|
| Queries | |
| Peak memory | |
| Rows rendered | |

Then **STOP**.
