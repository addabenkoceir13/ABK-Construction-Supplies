---
description: Wave A — Zero-risk query optimizations (eager loading, select, indexes, Blade loop fixes)
allowed-tools: Read, Write, Edit, Grep, Glob, Bash(php artisan test:*), Bash(./vendor/bin/pest:*), Bash(php artisan migrate:*), Bash(php artisan make:migration:*), Bash(git:*)
---

Implement **WAVE A only**. Do not start Wave B or C.

## Techniques allowed in this wave
- `with()`, `withCount()`, `withExists()`, `withSum()`, `loadMissing()`
- Explicit `select([...])` on the columns Blade actually uses
- Removing unnecessary entries from `$appends`
- Replacing per-row queries in Blade with pre-loaded data passed from the controller
- Hoisting `@can` / policy checks and repeated helper calls out of loops
- New migration adding indexes on FKs and on `where` / `orderBy` columns

## Explicitly NOT allowed in this wave
- New classes, services, DTOs, or abstractions
- Changing public method signatures
- Changing route names, request parameter names, or view variable names
- Changing what the page displays
- Adding any composer package

## Hard rules
1. The characterization tests must pass **unmodified** after every change. If a test fails, revert that change and report it — do not edit the test.
2. If you find an unbounded `->get()` on a large table, **stop and ask me** — that belongs to Wave B.
3. One logical change per commit. Conventional commit messages: `perf(orders): eager load customer relation to remove N+1`.
4. Run the test suite after each commit, not just at the end.
5. Any new index goes in a fresh migration file — never edit an existing migration.

## Per-change output
```
### [n] <short title>
File: path/to/file.php:LINE
Before: <the problematic snippet>
After:  <the fixed snippet>
Queries: 412 -> 8
Tests:   PASS
```

## Final report
Run the benchmark test and print:

| Metric | Baseline | After Wave A | Delta |
|--------|----------|--------------|-------|

Then **STOP** and wait for approval before Wave B.
