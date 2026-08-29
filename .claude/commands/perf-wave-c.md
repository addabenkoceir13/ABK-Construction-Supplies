---
description: Wave C — Structural refactor for readability and reuse, zero behavior change
allowed-tools: Read, Write, Edit, Grep, Glob, Bash(php artisan test:*), Bash(./vendor/bin/pest:*), Bash(php artisan make:*), Bash(./vendor/bin/pint:*), Bash(git:*)
---

Implement **WAVE C** on top of approved Waves A and B.

This wave changes **structure only**. Behavior stays byte-identical.

## Target architecture (Laravel + Blade)

```
Controller       thin. FormRequest for validation, delegate to Service,
                 return view(). Max ~15 lines per method.
FormRequest      all validation + authorization rules
Service          business logic, single-responsibility methods
Query class      complex read queries — App\Queries\<Feature>Query
   or Scopes     simple reusable filters — local scopes on the model
View Model/DTO   readonly class shaping exactly what Blade needs
Blade            presentation only. No queries, no business logic, no @php blocks
Job              anything asynchronous
```

## Blade-specific cleanup
- Extract repeated markup into Blade components (`php artisan make:component`), but **never** put a query or a service call inside a component class that renders in a loop
- Replace `@php` blocks with data prepared in the View Model
- Move formatting (dates, currency, status labels) into the DTO or an accessor, computed once
- Keep the same view file names and the same variable names passed to `view()` unless I approve a rename

## Hard constraints
- The characterization tests must pass **unmodified**. Zero exceptions in this wave.
- No renaming of routes, request parameters, view names, or view variables
- Follow existing project conventions: namespaces, PSR-12, and run Pint if the project uses it
- Do not add a composer package without asking first

## Process — do not skip step 2
1. Present the proposed structure: file tree + one line per class describing its responsibility
2. **WAIT for my approval of the structure**
3. Implement one class at a time. After each: run the tests, then commit.
4. Delete dead code only after grepping the whole project to confirm it is unreferenced

## Final report
- Files added / moved / deleted
- LOC in the controller: before → after
- Cyclomatic complexity of the main method: before → after
- Benchmark: baseline → final, all metrics
- Anything from `KNOWN_ISSUES.md` that is now trivial to fix, listed as a follow-up proposal (do not fix it here)
