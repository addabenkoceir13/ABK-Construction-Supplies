---
description: Build an Arabic-aware search card (fullname + multi-phone) for the debts table
argument-hint: [optional: extra route or model to include]
allowed-tools: Read, Write, Edit, Grep, Glob, Bash(php artisan:*), Bash(./vendor/bin/pest:*), Bash(./vendor/bin/phpunit:*), Bash(php artisan test:*), Bash(git:*)
---

You are a senior Laravel engineer on a **Laravel + Blade** app with an **Arabic (RTL) primary interface**, Algerian business domain.

# Known facts about the schema

These come from direct inspection of the production database. Verify them, do not assume anything beyond them.

- Table: **`debts`** — approximately **3,200 rows**
- Relevant columns: `id`, `user_id`, `tractor_driver_id`, `fullname`, `phone`, `date_debut_debt`, `total_debt_amount`, `debt_paid`, `rest_debt_amount`, `date_end_debt`
- **`fullname` and `phone` live directly on `debts`** — no join to a clients table is needed for search
- **Both debt types (regular debt and debt-with-supplier) are stored in this single table.** Find how they are distinguished — a type/flag column, `tractor_driver_id` being null, or a scope — and report it in Phase 1.

## Critical: the `phone` column holds MULTIPLE numbers

Real values look like:

```
0549380267
0745876577/0654689876
054789632          <- only 9 digits, dirty data, do not discard the row
```

Numbers are separated by `/`. There may be more than two. Separators may also appear as `-`, `،`, `,` or spaces in some rows — check the real data.

**Searching `0654689876` MUST find the row whose `phone` is `0745876577/0654689876`.** This is the single most important requirement of this task.

# Goal

Replace the DataTable's built-in client-side `Search:` box with a **dedicated search card** above the table, holding two explicit inputs — **الاسم الكامل** and **رقم الهاتف** — backed by an indexed, server-side, Arabic-tolerant query.

$ARGUMENTS

---

# PHASE 1 — DISCOVERY (read-only, no file changes)

Inspect the codebase and the database, then report:

1. The `Debt` model, its casts, scopes, appends, observers, and how the two debt types are separated.
2. `php artisan db:show --counts` and the structure of `debts`: exact types, charset, collation of `fullname` and `phone`, and every existing index.
3. **Sample the real data and report frequencies**, do not guess:
   - How many rows have more than one number in `phone`? Which separators actually occur, and how often?
   - How many phone values are not exactly 10 digits after stripping non-digits?
   - Do any `fullname` values contain harakat (تشكيل), tatweel (ــ), double spaces, or Latin letters?
   - Do alef variants (`أ إ آ ا`), `ة`/`ه`, `ى`/`ي` all appear across different rows for what is plausibly the same name? Give real examples from the table.
   - Are there Arabic-Indic digits (`٠١٢٣`) anywhere in `phone`?
4. The controller method and Blade view that render this page. Is the DataTable client-side (all 3,200 rows dumped into the DOM) or server-side?
5. **The related line-items**: the page shows several rows per debt plus a `المجموع` total row. Find that relation and report whether it is eager-loaded. Report the total row count of that child table — it is likely much larger than 3,200 and is the real performance problem on this page.

**Output** the findings, then **STOP** and wait for my approval.

---

# PHASE 2 — DESIGN PROPOSAL (still no code)

Propose the design following the decisions below. If any is wrong for this codebase, say so with a reason and propose an alternative.

## 2.1 `App\Support\ArabicNormalizer`

One pure static class, single source of truth, no dependencies. Method `name(string $raw): string` applies, in this order:

- trim, collapse repeated whitespace, `mb_strtolower`
- strip tatweel `U+0640`
- strip harakat / diacritics `U+064B–U+065F` and `U+0670`
- fold `أ إ آ ٱ ٲ ٳ` → `ا`
- fold `ة` → `ه`
- fold `ى` → `ي`
- fold `ؤ` → `و`, `ئ` → `ي`, drop standalone `ء`
- convert Arabic-Indic `٠-٩` and Extended `۰-۹` digits to Western `0-9` (this project enforces Western digits everywhere)
- drop punctuation that carries no meaning inside a name

Method `phone(string $raw): string` — normalizes **one** number: digits only, then a leading `00213`, `+213`, or `213` is rewritten to `0`.

Method `phones(string $raw): array` — **splits a multi-number field first**, on `/`, `\`, `,`, `،`, `;`, `|`, and whitespace, then normalizes each part, drops empties, and returns a de-duplicated list. `0745876577/0654689876` → `['0745876577', '0654689876']`.

The fold table above is the spec. Implement it exactly, do not invent extra rules.

## 2.2 Storage

**On `debts`:** add `fullname_normalized` VARCHAR(255) NULL, indexed.

**New table `debt_phones`** — this is how multi-number search stays indexable:

| column | notes |
|---|---|
| `id` | |
| `debt_id` | FK, indexed, cascade on delete |
| `phone_normalized` | VARCHAR(20), **indexed** |
| `position` | tinyint, preserves the original order |

Reason: storing the numbers concatenated in one column would force `LIKE '%0654%'`, a leading wildcard, which cannot use an index and would table-scan. A child row per number lets us do a prefix `LIKE '0654%'` against a real index.

The original `phone` column stays untouched — it remains what is displayed. `debt_phones` is a derived search index only.

Rules:
- Populate both from a `saving()` hook on the model (or a `HasNormalizedSearch` trait) so they can never drift.
- Do **not** use MySQL generated columns; the fold rules are too complex for SQL and would duplicate the logic.
- Artisan command `search:backfill-normalized` using `chunkById()` — never `chunk()`, never `get()` — idempotent and safe to re-run.
- Rows with malformed numbers (the 9-digit case) are **still indexed as-is**. Never drop a row. Report the count at the end of the backfill.

Propose the exact migration files. Do not edit existing migrations.

## 2.3 Query strategy — `App\Queries\DebtSearchQuery`

Since both debt types share the `debts` table, this is **one** query class with the type applied as a filter, not two parallel classes.

- **Name**: normalize the input, split on whitespace, require **every** token to match. `محمد بن قصير` must find `محمد عدة بن قصير` — tokens in any order, not necessarily adjacent.
- **Ranking** via a `CASE` in `orderByRaw`: exact whole-name match, then prefix match (`LIKE 'term%'`, index-friendly), then contains (`LIKE '%term%'`).
- **Phone**: normalize the input, then `whereExists` / `whereHas` on `debt_phones` with a prefix `LIKE`. Typing `0654` narrows progressively; typing `+213654689876` still finds `0654689876`.
- **Both filled** → AND.
- **Minimum length**: ignore a name token under 2 chars and a phone under 3 digits, so the DB is not hit on every keystroke.
- **Always paginated.** Never an unbounded result set.
- Escape `%` and `_` in the user's term before putting it into `LIKE`. Bindings only, never string concatenation.

Do **not** use MySQL FULLTEXT here: the default `ft_min_token_size = 4` silently drops short Arabic tokens like `بن` and `علي`, which is exactly the failure we are fixing. If you think the ngram parser is a better fit, argue for it and let me decide.

## 2.4 UI — the search card

A Blade component `<x-debt-search-card>`, placed **above** the table, outside it:

- Two labelled inputs side by side: `الاسم الكامل` and `رقم الهاتف`
- Buttons `بحث` and `مسح`
- RTL-correct, matching the existing dark theme — read the current Blade views and reuse their classes, do not invent a new visual style
- Debounced live search at 350ms **only if Alpine.js is already in the project**; otherwise a plain GET form. Check first, do not add a JS dependency.
- Filters live in the query string (`?name=...&phone=...`), so results are bookmarkable and pagination keeps them via `->withQueryString()`
- Above the table show the active filters and `عدد النتائج: N`
- Then set `searching: false` on the DataTable, so there are not two competing search boxes giving different answers

## 2.5 Server-side table

3,200 parent rows plus their line-items is too much for a client-side DataTable. Once the search is server-side, the table should be too. Report whether `yajra/laravel-datatables` is already installed. If it is not, **ask me before adding it** — a plain paginated Blade table may be the better answer here.

**Output** the full design: file tree, migration definitions, class responsibilities, component signature. Then **STOP** and wait for approval.

---

# PHASE 3 — TESTS FIRST

Detect Pest vs PHPUnit and follow the project's existing conventions.

**Unit tests for `ArabicNormalizer`:**

| Input | Must normalize equal to |
|---|---|
| `أحمد` | `احمد` |
| `فاطمة` | `فاطمه` |
| `مصطفى` | `مصطفي` |
| `مُحَمَّد` | `محمد` |
| `مـــحـــمـــد` | `محمد` |
| `عبد  الرحمن` | `عبد الرحمن` |

| Phone input | Expected output |
|---|---|
| `0745876577/0654689876` | `['0745876577','0654689876']` |
| `0745876577 / 0654689876` | same as above |
| `+213654689876` | `['0654689876']` |
| `٠٦٥٤٦٨٩٨٧٦` | `['0654689876']` |
| `054789632` | `['054789632']` — kept, not discarded |
| `` (empty) | `[]` |

**Feature tests against `debts`:**

- name search: full, partial, out-of-order tokens
- name written with a different alef/taa spelling than what is stored
- **phone search matching the SECOND number in a `a/b` value** — this test is mandatory
- phone in each format above
- combined name + phone
- empty query returns the normal unfiltered paginated list
- no-match returns an empty state, not an error
- filters survive pagination to page 2
- both debt types filter correctly and do not leak into each other
- a query-count assertion proving the search introduces no N+1

Show the tests failing correctly against the unimplemented feature, then **STOP**.

---

# PHASE 4 — IMPLEMENTATION

Commit and run the suite after every step:

1. `ArabicNormalizer` + unit tests green
2. Migrations: `fullname_normalized` on `debts`, new `debt_phones` table
3. `saving()` hook / trait + `search:backfill-normalized`; run it and report rows processed, phone rows created, and malformed numbers found
4. `DebtSearchQuery`
5. Controller wiring: a FormRequest reads `name` and `phone`, delegates to the query class, returns a paginator
6. `<x-debt-search-card>` and the view changes
7. Disable the DataTable's internal search

## Hard rules
- Controller stays thin — validation in the FormRequest, logic in the query class
- Never modify the original `phone` or `fullname` columns; they stay as displayed
- No new composer package without asking me first
- No route name, request param, or view variable renamed
- Every existing test passes unmodified

## Final report
- Files added / modified
- Page query count and response time: before → after
- `EXPLAIN` for the main search query, showing the index is used (`type` must not be `ALL`)
- Backfill stats, including the list of malformed phone values for me to review
