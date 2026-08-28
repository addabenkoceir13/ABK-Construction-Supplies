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
use Tests\TestCase;

/**
 * Characterization tests for DebtController@index / @indexPaid / @show.
 *
 * These lock in CURRENT behavior (including behavior that looks wrong) as a
 * safety net before the Wave A/B/C performance refactor. See KNOWN_ISSUES.md
 * for the pre-existing quirks/bugs discovered while writing this suite —
 * none of them are fixed here.
 */
class DebtControllerTest extends TestCase
{
    use RefreshDatabase;

    protected TractorDriver $normalDriver;
    protected TractorDriver $deliveryDriver;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Fixed seed so filler Faker data (phone numbers, note text, etc.)
        // is deterministic across runs.
        fake()->seed(4321);

        // EloquentDebt::debtPaid()/debtUnPaid() hardcode `whereTractorDriverId(1)`
        // instead of resolving the "normal" driver dynamically (see KNOWN_ISSUES.md).
        // The normal driver MUST be the first tractor_drivers row inserted so it
        // lands on id=1 and the controller's list pages actually pick it up.
        $this->normalDriver = TractorDriver::factory()->normal()->create([
            'fullname' => 'Walk-in Customer',
        ]);
        $this->deliveryDriver = TractorDriver::factory()->create([
            'fullname' => 'Delivery Driver',
        ]);

        $this->user = User::factory()->create(['email' => 'debt-tests@example.com']);
    }

    private function unpaidDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ], $overrides));
    }

    private function paidDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'paid',
        ], $overrides));
    }

    // ------------------------------------------------------------------
    // index() — unpaid debts for the "normal" (walk-in) tractor driver
    // ------------------------------------------------------------------

    public function test_index_renders_empty_state_when_no_unpaid_debts_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertOk();
        $response->assertViewIs('content.debt.index');
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 0);
    }

    public function test_index_renders_a_single_unpaid_debt_with_its_products(): void
    {
        $debt = $this->unpaidDebt([
            'fullname' => 'Ahmed Debtor',
            'total_debt_amount' => 500,
            'rest_debt_amount' => 500,
        ]);

        $category = Category::factory()->create(['name' => 'Cement']);
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Cement',
            'quantity' => '10',
            'amount' => 500,
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1);
        $response->assertSeeText('Ahmed Debtor');
        $response->assertSeeText('Cement');
    }

    public function test_index_paginates_unpaid_debts_at_25_per_page(): void
    {
        // Wave B: debtUnPaid() now paginates instead of loading every matching
        // row (see KNOWN_ISSUES.md for the pre-Wave-B behavior this replaced).
        $this->unpaidDebt();
        Debt::factory()->count(60)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        // Noise that debtUnPaid()'s filters must exclude.
        $this->paidDebt();
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->deliveryDriver->id,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertOk();
        $response->assertViewHas('debts', function ($debts) {
            return $debts->count() === 25
                && $debts->total() === 61
                && $debts->perPage() === 25;
        });
    }

    public function test_index_page_query_param_selects_the_requested_page(): void
    {
        Debt::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        $page1 = $this->actingAs($this->user)->get(route('debt.index'));
        $page2 = $this->actingAs($this->user)->get(route('debt.index', ['page' => 2]));
        $pageOutOfRange = $this->actingAs($this->user)->get(route('debt.index', ['page' => 999]));

        $page1->assertOk()->assertViewHas('debts', fn ($v) => $v->count() === 25 && $v->currentPage() === 1);
        $page2->assertOk()->assertViewHas('debts', fn ($v) => $v->count() === 5 && $v->currentPage() === 2);
        $pageOutOfRange->assertOk()->assertViewHas('debts', fn ($v) => $v->count() === 0 && $v->currentPage() === 999);
    }

    public function test_index_orders_unpaid_debts_by_id_descending(): void
    {
        $first = $this->unpaidDebt(['fullname' => 'First Debtor']);
        $second = $this->unpaidDebt(['fullname' => 'Second Debtor']);

        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertViewHas('debts', function ($debts) use ($first, $second) {
            return $debts->first()->is($second) && $debts->last()->is($first);
        });
    }

    public function test_index_excludes_paid_debts_and_debts_for_other_drivers(): void
    {
        $matching = $this->unpaidDebt(['fullname' => 'Should Show']);
        $this->paidDebt(['fullname' => 'Should Not Show - Paid']);
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->deliveryDriver->id,
            'status' => 'unpaid',
            'fullname' => 'Should Not Show - Other Driver',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index'));

        $response->assertSeeText('Should Show');
        $response->assertDontSeeText('Should Not Show - Paid');
        $response->assertDontSeeText('Should Not Show - Other Driver');
        $response->assertViewHas('debts', fn ($v) => $v->count() === 1 && $v->first()->is($matching));
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('debt.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_is_accessible_to_any_authenticated_user_no_ownership_or_role_check(): void
    {
        // KNOWN_ISSUES.md: there is no Policy/Gate on Debt — any authenticated
        // user can view/edit/delete every debt regardless of who created it.
        $otherUser = User::factory()->create(['email' => 'other-user@example.com']);
        $this->unpaidDebt(['fullname' => 'Owned By First User']);

        $response = $this->actingAs($otherUser)->get(route('debt.index'));

        $response->assertOk();
        $response->assertSeeText('Owned By First User');
    }

    // ------------------------------------------------------------------
    // indexPaid() — paid debts for the "normal" (walk-in) tractor driver
    // ------------------------------------------------------------------

    public function test_index_paid_renders_empty_state_when_no_paid_debts_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('debt.index-paid'));

        $response->assertOk();
        $response->assertViewIs('content.debt.indexPaid');
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 0);
    }

    public function test_index_paid_renders_a_single_paid_debt_with_its_products(): void
    {
        $debt = $this->paidDebt([
            'fullname' => 'Fatima Payer',
            'total_debt_amount' => 300,
            'debt_paid' => 300,
            'rest_debt_amount' => 0,
        ]);

        $category = Category::factory()->create(['name' => 'Sand']);
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Sand',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index-paid'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1);
        $response->assertSeeText('Fatima Payer');
        $response->assertSeeText('Sand');
    }

    public function test_index_paid_paginates_debts_at_25_per_page(): void
    {
        // Wave B: debtPaid() now paginates instead of loading every matching row.
        Debt::factory()->count(75)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.index-paid'));

        $response->assertOk();
        $response->assertViewHas('debts', function ($debts) {
            return $debts->count() === 25
                && $debts->total() === 75
                && $debts->perPage() === 25;
        });
    }

    public function test_index_paid_orders_debts_by_id_descending(): void
    {
        $first = $this->paidDebt(['fullname' => 'First Paid']);
        $second = $this->paidDebt(['fullname' => 'Second Paid']);

        $response = $this->actingAs($this->user)->get(route('debt.index-paid'));

        $response->assertViewHas('debts', function ($debts) use ($first, $second) {
            return $debts->first()->is($second) && $debts->last()->is($first);
        });
    }

    public function test_index_paid_requires_authentication(): void
    {
        $response = $this->get(route('debt.index-paid'));

        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------
    // show() — single debt detail page
    // ------------------------------------------------------------------

    public function test_show_renders_a_debt_with_its_products_and_subcategory_names(): void
    {
        $debt = $this->unpaidDebt(['fullname' => 'Karim Client']);

        $category = Category::factory()->create(['name' => 'Gravel']);
        $subcategory = SubCategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Ton',
        ]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Gravel',
            'quantity' => '3',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt.show', $debt->id));

        $response->assertOk();
        $response->assertViewIs('content.Debt.view');
        $response->assertViewHas('debt', fn ($v) => $v->is($debt));
        $response->assertSeeText('Karim Client');
        $response->assertSeeText('Gravel');
    }

    public function test_show_with_a_nonexistent_debt_id_currently_errors_instead_of_404(): void
    {
        // KNOWN_ISSUES.md: DebtController::show() does `Debt::find($id)` with no
        // findOrFail()/404 handling. A missing id currently blows up in the view
        // (`Attempt to read property "fullname" on null`) as a 500, not a 404.
        $response = $this->actingAs($this->user)->get(route('debt.show', 999999));

        $response->assertStatus(500);
    }

    public function test_show_requires_authentication(): void
    {
        $debt = $this->unpaidDebt();

        $response = $this->get(route('debt.show', $debt->id));

        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------
    // debt.search — used by the index page's typeahead, already bounded
    // ------------------------------------------------------------------

    public function test_search_name_returns_up_to_ten_matches(): void
    {
        Debt::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'fullname' => 'Search Match Client',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('debt.search'), ['query' => 'Search Match']);

        $response->assertOk();
        $response->assertJsonCount(10, 'query');
    }
}
