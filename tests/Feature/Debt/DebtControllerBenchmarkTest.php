<?php

namespace Tests\Feature\Debt;

use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtHistory;
use App\Models\DebtProduct;
use App\Models\SubCategory;
use App\Models\TractorDriver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Benchmark for DebtController@indexPaid — the page /perf-audit identified as
 * the most likely cause of the reported freeze (unbounded ->get() + N+1
 * eager-loading of getDebtProduct/debtHistories + per-row Blade @include of
 * two full modal partials).
 *
 * PRE-REFACTOR BASELINE, recorded 2026-08-28 against a 50-paid-debt sample
 * (2 products + 1 payment history each). Production currently holds ~1701
 * paid rows for the same driver, so real query counts scale roughly linearly
 * above this sample — this is a representative slice, not the full prod load.
 *
 * Wave A (eager loading + pagination) must cut this baseline by roughly two
 * orders of magnitude. When Wave A lands, re-run this test, update
 * BASELINE_QUERY_COUNT to the new number, and update this docblock.
 */
class DebtControllerBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_SIZE = 50;

    // BASELINE, updated as Wave A/B changes land — see class docblock.
    // 103 (pre-Wave A) -> 54 after eager-loading getDebtProduct
    // -> 5 after also eager-loading debtHistories (end of Wave A)
    // -> 6 after Wave B paginate(25) adds one COUNT(*) query.
    // Note: from here on, this number no longer scales with SAMPLE_SIZE —
    // that is the whole point of Wave B.
    private const BASELINE_QUERY_COUNT = 6;

    public function test_index_paid_query_count_matches_recorded_pre_refactor_baseline(): void
    {
        $this->seedRealisticPaidDebtLoad();

        $user = User::factory()->create();

        DB::enableQueryLog();
        $start = microtime(true);

        $response = $this->actingAs($user)->get(route('debt.index-paid'));

        $elapsedMs = (microtime(true) - $start) * 1000;
        $peakMemory = memory_get_peak_usage(true);
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();

        // Tight equality on purpose: the point of this test is to be the
        // first thing that visibly moves when Wave A lands. A drift here is
        // either the expected Wave A improvement (update the baseline) or an
        // unrelated regression (investigate).
        $this->assertSame(
            self::BASELINE_QUERY_COUNT,
            $queryCount,
            'Baseline query count drifted from the pre-refactor recorded value — see class docblock.'
        );

        // Loose guard, not a tight budget — just catches gross regressions.
        $this->assertLessThan(64 * 1024 * 1024, $peakMemory, 'Peak memory ballooned unexpectedly.');

        fwrite(STDERR, sprintf(
            "\n[BENCHMARK baseline] indexPaid rows=%d queries=%d peak_memory=%s wall_time=%.1fms\n",
            self::SAMPLE_SIZE,
            $queryCount,
            number_format($peakMemory),
            $elapsedMs
        ));
    }

    private function seedRealisticPaidDebtLoad(): void
    {
        $normalDriver = TractorDriver::factory()->normal()->create();
        $author = User::factory()->create(['email' => 'benchmark-author@example.com']);

        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        Debt::factory()
            ->count(self::SAMPLE_SIZE)
            ->create([
                'user_id' => $author->id,
                'tractor_driver_id' => $normalDriver->id,
                'status' => 'paid',
            ])
            ->each(function (Debt $debt) use ($subcategory) {
                DebtProduct::factory()->count(2)->create([
                    'debt_id' => $debt->id,
                    'subcategory_id' => $subcategory->id,
                ]);
                DebtHistory::factory()->create(['debt_id' => $debt->id]);
            });
    }
}
