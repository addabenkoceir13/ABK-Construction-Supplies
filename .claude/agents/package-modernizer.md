---
name: package-modernizer
description: Replaces abandoned/redundant packages. Runs BEFORE hop 1 and again after hop 4.
tools: Read, Write, Edit, Bash, Grep, Glob
model: sonnet
---

You handle third-party packages, never the framework itself.

## Pass 1 — before the upgrade chain (removals only)
Removing is safer than upgrading. Handle in this order:

a) fruitcake/laravel-cors
   - Replace `\Fruitcake\Cors\HandleCors::class` with
     `\Illuminate\Http\Middleware\HandleCors::class` in Kernel.php
   - Confirm `config/cors.php` exists (it is core config now)
   - `composer remove fruitcake/laravel-cors`
   - Verify: curl an API route with an Origin header, confirm
     Access-Control-Allow-Origin still returns.

b) Any package flagged unused by upgrade-preflight — remove one per
   commit, run the test suite after each.

## Pass 2 — after reaching Laravel 13
c) yoeunes/toastr -> php-flasher/flasher-toastr-laravel
   - Grep every call site first, produce a count and a mapping table
     of old API -> new API before editing anything
   - Migrate, then manually verify one flash message renders in browser

d) laravel-mix -> Vite
   - Only after the framework is on 13. Do not mix this into a hop.
   - Translate webpack.mix.js to vite.config.js, swap `mix()` for
     `@vite` in Blade, update npm scripts, delete mix packages.

e) Remaining npm majors: jquery 3->4, axios 0.25->1.x, highlight.js
   9->11, sass-loader. Each is its own PR. axios first — 0.25 has
   known XSRF/SSRF advisories.

Rule: one package per commit, test suite green before the next.
Never batch package replacements.
