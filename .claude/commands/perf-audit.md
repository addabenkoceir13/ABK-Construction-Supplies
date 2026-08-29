---
description: Phase 1 — Read-only performance audit of a slow Laravel + Blade page
argument-hint: <route name | Controller@method | URI>
allowed-tools: Read, Grep, Glob, Bash(php artisan route:list:*), Bash(php artisan db:show:*), Bash(php artisan model:show:*)
---

You are a senior Laravel performance engineer working on a **Laravel + Blade** application (no Livewire, no Inertia, no SPA).

## Target
$ARGUMENTS

## Task
**AUDIT ONLY. Do not create, modify, or delete a single file in this phase.**

Trace the complete request lifecycle for the target page and produce a findings report.

### 1. Entry point
- Locate the route definition (`routes/*.php`) and resolve it with `php artisan route:list --path=...`
- List the middleware stack and flag anything that hits the DB or an external API on every request
- Locate the controller method and read it in full

### 2. Data layer
For every Eloquent / Query Builder call reachable from this request, record:
- File:line, the query, and the estimated number of rows returned
- Whether the result set is **bounded** (paginate/limit/chunk) or **unbounded** (`->get()`, `->all()`)

Explicitly flag these anti-patterns:
- **N+1**: relations accessed inside `@foreach` without `with()`
- Relation counts computed per row instead of `withCount()`
- `SELECT *` pulling TEXT / JSON / BLOB columns the view never uses
- Queries executed **inside Blade templates** (`{{ $post->comments()->count() }}`, `@php` blocks, view composers)
- Queries inside accessors, observers, or model events triggered per row
- Missing indexes on columns used in `where`, `orderBy`, `groupBy`, and on foreign keys

### 3. Model layer
- `$appends` and every accessor it triggers — note which ones run a query or heavy computation
- Global scopes, `booted()` hooks, observers, `spatie/laravel-activitylog` or similar package hooks

### 4. Blade render layer
- Every `@foreach` / `@forelse` over a large collection — record the collection and its size
- `@include` or Blade components rendered **inside a loop** (each one is a separate view compile + render)
- Relations lazy-loaded inside the loop
- `@can` / policy checks per row (each may hit the DB)
- Heavy helpers per row: `route()`, `Carbon` formatting, `Str::` chains, currency/number formatting
- Missing `@once`, missing `loop` optimizations, unnecessary nested loops

### 5. Infrastructure risk
- Anything that can exhaust `memory_limit` or `max_execution_time` (this is the usual cause of the site freezing / 502 / 504)
- Session driver, cache driver, and whether `config:cache` / `view:cache` / `route:cache` are in use

## Output format

**A. Findings table**

| # | File:Line | Layer | Issue | Severity | Est. impact |
|---|-----------|-------|-------|----------|-------------|

Severity: Critical (can crash the app) / High / Medium / Low

**B. Top 5 by impact-to-effort ratio** — one line each.

**C. Estimated current cost** — query count, largest unbounded result set, and the single most likely cause of the freeze.

## Stop condition
Print the report, then **STOP**. Do not propose or write code. Wait for my approval.
