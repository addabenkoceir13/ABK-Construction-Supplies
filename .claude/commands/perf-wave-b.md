---
description: Wave B — Bound the data, cache the expensive, queue the heavy
allowed-tools: Read, Write, Edit, Grep, Glob, Bash(php artisan test:*), Bash(./vendor/bin/pest:*), Bash(php artisan make:job:*), Bash(git:*)
---

Implement **WAVE B only**, on top of an approved and committed Wave A.

## Goal
Make the page's cost independent of table size. After this wave, doubling the number of rows in the database must not double the page's response time.

## Techniques

**Bounding**
- `paginate()` for standard listings; `simplePaginate()` when the total count query is itself expensive; `cursorPaginate()` for very large or infinitely-scrolled sets
- `chunkById()` / `lazyById()` for exports and background processing — never `chunk()` when the loop mutates the rows it iterates
- A hard `limit()` on any "recent items" widget

**Caching**
- `Cache::remember()` for aggregates, counts, and dropdown/reference data
- Cache keys must include every variable that affects the result (filters, user id or role, locale, page)
- Every cached value needs an invalidation path — a model observer, an event listener, or a short TTL. State it explicitly for each key you add.
- Blade: `@once` for repeated blocks, and cache rendered fragments only where the fragment is genuinely expensive

**Queueing**
- Move to a queued Job anything that is not needed to render the response: emails, notifications, PDF generation, image processing, exports, external API calls, activity logging
- The controller dispatches and returns immediately

## Hard rules
1. Characterization tests must still pass. **Exception:** if introducing pagination legitimately changes what the page returns, show me the exact test diff and get my approval before editing it.
2. Do not change the visible ordering of records unless I approve it — `orderBy` on a non-unique column plus pagination causes duplicate rows across pages; if you find this, add a deterministic tiebreaker (`->orderBy('id')`) and tell me.
3. Blade pagination links must use the project's existing pagination view; preserve query strings with `->withQueryString()`.
4. Do not add a caching or queue package. Use what's already configured.
5. One logical change per commit, tests after each.

## Final report
| Metric | Baseline | Wave A | Wave B | Delta |
|--------|----------|--------|--------|-------|

Also state, for each cache key added: the key pattern, the TTL, and the invalidation trigger.

Then **STOP**.
