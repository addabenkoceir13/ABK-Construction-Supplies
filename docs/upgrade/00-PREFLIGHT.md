# Preflight — Laravel 9 → 13 Upgrade

## Temporary audit bypasses — MUST REMOVE AT L13

`composer.json` pins `laravel/framework` to the exact tagged release `9.52.22`
(replacing an untagged `9.x-dev` lock). Composer's `audit.block-insecure`
refused to resolve that tag because 4 advisories are open against it. Every
non-EOL Laravel 9 release carries these same advisories — Laravel 9 is EOL,
and the underlying issues are resolved by the framework itself upstream by
the time this chain reaches Laravel 13. The bypass exists only so the lock
file can reference a real, reproducible tag instead of a dev-branch snapshot;
it is not a statement that these advisories are acceptable long-term.

Bypassed via `composer.json` → `config.audit.ignore`:

| Advisory ID | Reason |
|---|---|
| `PKSA-m5cs-t1y6-qpcs` | temp: L9 EOL, resolved at L13 — remove after upgrade |
| `PKSA-3r5d-mb8f-1qw9` | temp: L9 EOL, resolved at L13 — remove after upgrade |
| `PKSA-mdq4-51ck-6kdq` | temp: L9 EOL, resolved at L13 — remove after upgrade |
| `PKSA-8qx3-n5y5-vvnd` | temp: L9 EOL, resolved at L13 — remove after upgrade |

`"block-insecure": false` was NOT set — the ignore list is scoped to exactly
these 4 IDs, nothing else is suppressed.

**To undo, once the chain reaches Laravel 13:**

```bash
composer config --unset config.audit.ignore
composer audit
```

Then confirm `composer audit` reports zero advisories against
`laravel/framework` before considering the chain complete. If it does not,
do not remove the bypass until the remaining advisory is triaged.

## Known pre-existing test failure (unrelated to this hop)

`Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response`
fails (expects `200` on `GET /`, receives `302`) because `routes/web.php`
protects `/` with `->middleware('auth')`, while the test is the unmodified
Laravel boilerplate stub (last touched 2024-09-21, predates any upgrade work).
This failure exists independently of the `laravel/framework` version pin and
was confirmed present before the audit-bypass commit. Not fixed as part of
this commit per the "never skip or delete a test" rule — tracked here so
later hops don't mistake it for a regression.
