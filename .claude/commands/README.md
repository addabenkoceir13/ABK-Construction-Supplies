# Laravel + Blade Performance Refactor — Claude Code Commands

## Install

Copy the six `.md` files into your project:

```bash
mkdir -p .claude/commands
cp perf-*.md .claude/commands/
```

Commit them so the whole team gets the same workflow.

Verify inside Claude Code:

```
/help
```

You should see `/perf-audit`, `/perf-plan`, `/perf-safety-net`, `/perf-wave-a`, `/perf-wave-b`, `/perf-wave-c`.

## Order of use

```
/perf-audit App\Http\Controllers\OrderController@index
        ↓  read the report, approve
/perf-plan
        ↓  approve wave by wave
/perf-safety-net App\Http\Controllers\OrderController@index
        ↓  baseline numbers recorded
/perf-wave-a        ← usually 70–90% of the gain
        ↓
/perf-wave-b        ← makes cost independent of table size
        ↓
/perf-wave-c        ← readability, optional
```

Do not skip `/perf-safety-net`. Without it, Wave C is a rewrite, not a refactor.

## Before you start

Work on a dedicated branch:

```bash
git checkout -b refactor/orders-page-performance
```

Enable measurement locally:

```bash
composer require --dev barryvdh/laravel-debugbar
```

## If the site is currently freezing

Run `/perf-audit` first and look specifically for an unbounded `->get()` on a large table. That single line is the most common cause of memory exhaustion and 502/504 in Laravel + Blade listing pages. Fixing it usually belongs in Wave A even though pagination is a Wave B technique — the audit command is instructed to promote it.

## Notes

- These commands assume Blade server-side rendering. No Livewire, no Inertia.
- Each command ends with an explicit STOP. That is intentional — approve each phase before the next.
- `$ARGUMENTS` in the audit and safety-net commands takes a route name, `Controller@method`, or a URI.
