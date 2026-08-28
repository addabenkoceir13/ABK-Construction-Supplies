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
 * Benchmark for DebtWithSupplierController@indexPaid — the /perf-audit
 * target, structurally identical to DebtController's pre-Wave-A freeze
 * cause (unbounded ->get() + N+1) but with one extra N+1 (tractorDriver,
 * never eager-loaded anywhere in this controller).
 *
 * PRE-REFACTOR BASELINE, recorded 2026-08-28 against a 50-paid-supplier-debt
 * sample (2 products + 1 payment history each). Production currently holds
 * ~1188 paid-supplier rows, so real query counts scale roughly linearly
 * above this sample.
 */
class DebtWithSupplierControllerBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    private const SAMPLE_SIZE = 50;

    // BASELINE, updated as Wave A/B changes land — see class docblock.
    // 153 (pre-Wave A) -> 6 after eager-loading tractorDriver/getDebtProduct/debtHistories (end of Wave A)
    // -> 7 after Wave B paginate(25) adds one COUNT(*) query.
    // Note: from here on, this number no longer scales with SAMPLE_SIZE —
    // that is the whole point of Wave B.
    private const BASELINE_QUERY_COUNT = 7;

    public function test_index_paid_query_count_matches_recorded_pre_refactor_baseline(): void
    {
        $this->seedRealisticPaidSupplierDebtLoad();

        $user = User::factory()->create();

        DB::enableQueryLog();
        $start = microtime(true);

        $response = $this->actingAs($user)->get(route('debt-supplier.index-paid'));

        $elapsedMs = (microtime(true) - $start) * 1000;
        $peakMemory = memory_get_peak_usage(true);
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();

        $this->assertSame(
            self::BASELINE_QUERY_COUNT,
            $queryCount,
            'Baseline query count drifted from the pre-refactor recorded value — see class docblock.'
        );

        $this->assertLessThan(64 * 1024 * 1024, $peakMemory, 'Peak memory ballooned unexpectedly.');

        fwrite(STDERR, sprintf(
            "\n[BENCHMARK baseline] indexPaid(supplier) rows=%d queries=%d peak_memory=%s wall_time=%.1fms\n",
            self::SAMPLE_SIZE,
            $queryCount,
            number_format($peakMemory),
            $elapsedMs
        ));
    }

    private function seedRealisticPaidSupplierDebtLoad(): void
    {
        TractorDriver::factory()->normal()->create(); // lands on id=1, excluded by the != filter
        $supplierDriver = TractorDriver::factory()->create(['type' => 'delivery', 'status' => 'active']);
        $author = User::factory()->create(['email' => 'benchmark-supplier-author@example.com']);

        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        Debt::factory()
            ->count(self::SAMPLE_SIZE)
            ->create([
                'user_id' => $author->id,
                'tractor_driver_id' => $supplierDriver->id,
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
