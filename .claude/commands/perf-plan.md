---
description: Phase 2 — Turn the audit into a wave-by-wave execution plan (still no code)
allowed-tools: Read, Grep, Glob
---

You are continuing the Laravel + Blade performance work. The audit from `/perf-audit` is in context.

## Task
Produce an execution plan. **Still no code changes. No file writes.**

For each finding you intend to fix, give exactly:
- **Root cause** — one sentence
- **Fix** — the concrete Laravel technique, naming the exact method (`withCount('comments')`, not "optimize the count")
- **Behavior-change risk** — None / Low / Medium / High
- **Migration needed?** — yes/no, and if yes the exact index definition (`$table->index(['user_id', 'created_at'])`)
- **Rollback** — how to undo this single change

## Group into three waves

**WAVE A — zero-risk query wins**
Eager loading, `withCount` / `withExists`, explicit `select()`, removing entries from `$appends`, adding DB indexes. No new classes, no signature changes.

**WAVE B — bounding and caching**
Pagination (`paginate` / `simplePaginate` / `cursorPaginate`), `chunkById` for exports, `Cache::remember` for expensive aggregates, moving heavy side-effects to queued Jobs, `@once` and view fragment caching in Blade.

**WAVE C — structural refactor**
Extract query logic into scopes or a query class, extract business logic into a Service, thin the controller, introduce DTOs / View Models for what Blade receives.

## Per-wave estimate
For each wave state the expected delta: queries removed, ms saved, memory saved. Be explicit that these are estimates to be verified against the benchmark.

## Ordering rule
Anything that can crash the app (unbounded `->get()`, memory exhaustion) is promoted to the front of Wave A regardless of which wave it structurally belongs to.

## Stop condition
Present the plan and **STOP**. I approve one wave at a time. Never start implementing without explicit approval of that specific wave.
