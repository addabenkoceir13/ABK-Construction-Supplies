<?php

namespace Tests\Feature\Debt;

use App\Models\Debt;
use App\Models\TractorDriver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Feature tests for the /search-arabic server-side search card on the four
 * debt list pages (debt.index, debt.index-paid, debt-supplier.index,
 * debt-supplier.index-paid). Written against the NOT-YET-IMPLEMENTED
 * feature (Phase 3) — expected to fail until Phase 4 lands
 * fullname_normalized/phone_normalized + DebtSearchQuery + the ?name=&phone=
 * query params on these routes.
 */
class DebtSearchTest extends TestCase
{
    use RefreshDatabase;

    protected TractorDriver $normalDriver;
    protected TractorDriver $supplierDriver;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        fake()->seed(13579);

        $this->normalDriver = TractorDriver::factory()->normal()->create();
        $this->supplierDriver = TractorDriver::factory()->create(['type' => 'delivery', 'status' => 'active']);
        $this->user = User::factory()->create(['email' => 'debt-search-tests@example.com']);
    }

    private function regularDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ], $overrides));
    }

    private function supplierDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->supplierDriver->id,
            'status' => 'unpaid',
        ], $overrides));
    }

    // ------------------------------------------------------------------
    // Name search
    // ------------------------------------------------------------------

    public function test_name_search_matches_full_name(): void
    {
        $target = $this->regularDebt(['fullname' => 'محمد عدة بن قصير']);
        $this->regularDebt(['fullname' => 'خالد بزيتوني']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'محمد عدة بن قصير']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_name_search_matches_a_partial_token(): void
    {
        $target = $this->regularDebt(['fullname' => 'محمد عدة بن قصير']);
        $this->regularDebt(['fullname' => 'خالد بزيتوني']);

        // "قص" is a substring of "قصير" — contains-match on the token.
        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'قص']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_name_search_matches_tokens_in_any_order(): void
    {
        $target = $this->regularDebt(['fullname' => 'محمد عدة بن قصير']);
        $this->regularDebt(['fullname' => 'خالد بزيتوني']);

        // Every token must match, regardless of order or adjacency.
        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'بن قصير محمد']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_name_search_matches_a_different_alef_and_ta_spelling_than_stored(): void
    {
        $target = $this->regularDebt(['fullname' => 'أحمد فاطمة']);
        $this->regularDebt(['fullname' => 'خالد بزيتوني']);

        // Stored with hamza-alef and ta-marbuta; searched with bare alef and ha.
        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'احمد فاطمه']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_name_search_ignores_a_single_character_token(): void
    {
        Debt::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        // A 1-char token is below the minimum length and must be dropped —
        // the query falls back to unfiltered (still paginated) results.
        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'م']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->total() === 30);
    }

    // ------------------------------------------------------------------
    // Phone search
    // ------------------------------------------------------------------

    public function test_phone_search_matches_the_second_number_in_a_delimited_value(): void
    {
        // The single most important requirement of this feature.
        $target = $this->regularDebt(['phone' => '0745876577/0654689876']);
        $this->regularDebt(['phone' => '0611111111']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '0654689876']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_phone_search_matches_with_international_prefix_input(): void
    {
        $target = $this->regularDebt(['phone' => '0654689876']);
        $this->regularDebt(['phone' => '0611111111']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '+213654689876']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_phone_search_matches_arabic_indic_digit_input(): void
    {
        $target = $this->regularDebt(['phone' => '0654689876']);
        $this->regularDebt(['phone' => '0611111111']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '٠٦٥٤٦٨٩٨٧٦']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_phone_search_finds_a_malformed_nine_digit_number(): void
    {
        $target = $this->regularDebt(['phone' => '054789632']);
        $this->regularDebt(['phone' => '0611111111']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '054789632']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_phone_search_narrows_progressively_on_a_prefix(): void
    {
        $target = $this->regularDebt(['phone' => '0654689876']);
        $this->regularDebt(['phone' => '0611111111']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '0654']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_phone_search_ignores_a_too_short_query(): void
    {
        Debt::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        // Under 3 digits is below the minimum length and must be dropped.
        $response = $this->actingAs($this->user)->get(route('debt.index', ['phone' => '06']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->total() === 30);
    }

    // ------------------------------------------------------------------
    // Combined, empty, no-match, pagination, cross-type isolation, N+1
    // ------------------------------------------------------------------

    public function test_combined_name_and_phone_search_applies_both_as_and(): void
    {
        $target = $this->regularDebt(['fullname' => 'محمد قصير', 'phone' => '0654689876']);
        // Same name, different phone — must be excluded.
        $this->regularDebt(['fullname' => 'محمد قصير', 'phone' => '0611111111']);
        // Same phone, different name — must be excluded.
        $this->regularDebt(['fullname' => 'خالد بزيتوني', 'phone' => '0654689876']);

        $response = $this->actingAs($this->user)->get(route('debt.index', [
            'name' => 'محمد قصير',
            'phone' => '0654689876',
        ]));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1 && $debts->first()->is($target));
    }

    public function test_empty_query_returns_the_normal_unfiltered_paginated_list(): void
    {
        Debt::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertOk();
        $response->assertViewHas('debts', function ($debts) {
            return $debts->total() === 30 && $debts->count() === 25;
        });
    }

    public function test_no_match_returns_an_empty_state_not_an_error(): void
    {
        $this->regularDebt(['fullname' => 'خالد بزيتوني']);

        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'اسم غير موجود']));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 0 && $debts->total() === 0);
    }

    public function test_search_filters_survive_pagination_to_page_two(): void
    {
        Debt::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
            'fullname' => 'مطابق للبحث',
        ]);
        // Noise that must never appear regardless of page.
        Debt::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
            'fullname' => 'غير مطابق',
        ]);

        $page1 = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'مطابق للبحث', 'page' => 1]));
        $page2 = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'مطابق للبحث', 'page' => 2]));

        $page1->assertOk()->assertViewHas('debts', fn ($d) => $d->count() === 25 && $d->total() === 30);
        $page2->assertOk()->assertViewHas('debts', fn ($d) => $d->count() === 5 && $d->total() === 30);
    }

    public function test_regular_and_supplier_debt_types_do_not_leak_into_each_others_search(): void
    {
        $regular = $this->regularDebt(['fullname' => 'احمد المشترك']);
        $supplier = $this->supplierDebt(['fullname' => 'احمد المشترك']);

        $regularResponse = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'احمد المشترك']));
        $supplierResponse = $this->actingAs($this->user)->get(route('debt-supplier.index', ['name' => 'احمد المشترك']));

        $regularResponse->assertOk()->assertViewHas('debts', fn ($d) => $d->count() === 1 && $d->first()->is($regular));
        $supplierResponse->assertOk()->assertViewHas('debts', fn ($d) => $d->count() === 1 && $d->first()->is($supplier));
    }

    public function test_search_introduces_no_n_plus_one_queries(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->regularDebt(['fullname' => 'بحث مكرر ' . $i]);
        }

        DB::enableQueryLog();
        $response = $this->actingAs($this->user)->get(route('debt.index', ['name' => 'بحث مكرر']));
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $response->assertOk();
        $response->assertViewHas('debts', fn ($d) => $d->count() === 10);
        // Flat regardless of the 10 matching rows — same order of magnitude
        // as the existing eager-loaded, paginated baseline (~6 queries).
        $this->assertLessThanOrEqual(10, $queryCount, "Query count ({$queryCount}) suggests an N+1 was introduced by the search.");
    }
}
