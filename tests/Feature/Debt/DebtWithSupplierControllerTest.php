<?php

namespace Tests\Feature\Debt;

use App\Models\Category;
use App\Models\Debt;
use App\Models\DebtProduct;
use App\Models\SubCategory;
use App\Models\TractorDriver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Characterization tests for DebtWithSupplierController@index / @indexPaid /
 * @show / @edit.
 *
 * These lock in CURRENT behavior (including behavior that looks wrong) as a
 * safety net before the /perf-plan-approved refactor. See KNOWN_ISSUES.md
 * for the pre-existing quirks/bugs discovered while writing this suite —
 * none of them are fixed here (including the two the user approved fixing
 * later via the plan: the blank Supplier field and destroy()'s wrong
 * redirect target — those are Wave A implementation work, not safety-net
 * work).
 */
class DebtWithSupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    protected TractorDriver $normalDriver;
    protected TractorDriver $supplierDriver;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        fake()->seed(9876);

        // driverDebtPaid()/driverDebtUnPaid() hardcode `where('tractor_driver_id', '!=', 1)`,
        // so the "normal" (walk-in customer) driver MUST be the first tractor_drivers
        // row inserted to land on id=1 and be excluded, same as DebtController's tests.
        $this->normalDriver = TractorDriver::factory()->normal()->create([
            'fullname' => 'Walk-in Customer',
        ]);
        $this->supplierDriver = TractorDriver::factory()->create([
            'fullname' => 'Supplier Driver One',
            'type' => 'delivery',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create(['email' => 'debt-supplier-tests@example.com']);
    }

    private function unpaidSupplierDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->supplierDriver->id,
            'status' => 'unpaid',
        ], $overrides));
    }

    private function paidSupplierDebt(array $overrides = []): Debt
    {
        return Debt::factory()->create(array_merge([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->supplierDriver->id,
            'status' => 'paid',
        ], $overrides));
    }

    // ------------------------------------------------------------------
    // index() — unpaid debts for any driver that is NOT the "normal" one
    // ------------------------------------------------------------------

    public function test_index_renders_empty_state_when_no_unpaid_supplier_debts_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertOk();
        $response->assertViewIs('content.DebtWithSupplier.index');
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 0);
    }

    public function test_index_renders_a_single_unpaid_supplier_debt_with_its_products(): void
    {
        $debt = $this->unpaidSupplierDebt([
            'fullname' => 'Amine Client',
            'total_debt_amount' => 500,
            'rest_debt_amount' => 500,
        ]);

        $category = Category::factory()->create(['name' => 'Brick']);
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Brick',
            'quantity' => '10',
            'amount' => 500,
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1);
        $response->assertSeeText('Amine Client');
        $response->assertSeeText('Brick');
    }

    public function test_index_renders_every_matching_unpaid_supplier_debt_with_no_pagination_today(): void
    {
        // KNOWN_ISSUES.md: driverDebtUnPaid() calls ->get() with no limit/paginate.
        Debt::factory()->count(40)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->supplierDriver->id,
            'status' => 'unpaid',
        ]);

        // Noise the filters must exclude.
        $this->paidSupplierDebt();
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($v) => $v->count() === 40);
    }

    public function test_index_orders_unpaid_supplier_debts_by_id_descending(): void
    {
        $first = $this->unpaidSupplierDebt(['fullname' => 'First Debtor']);
        $second = $this->unpaidSupplierDebt(['fullname' => 'Second Debtor']);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertViewHas('debts', function ($debts) use ($first, $second) {
            return $debts->first()->is($second) && $debts->last()->is($first);
        });
    }

    public function test_index_excludes_paid_debts_and_debts_for_the_normal_driver(): void
    {
        $matching = $this->unpaidSupplierDebt(['fullname' => 'Should Show']);
        $this->paidSupplierDebt(['fullname' => 'Should Not Show - Paid']);
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->normalDriver->id,
            'status' => 'unpaid',
            'fullname' => 'Should Not Show - Normal Driver',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertSeeText('Should Show');
        $response->assertDontSeeText('Should Not Show - Paid');
        $response->assertDontSeeText('Should Not Show - Normal Driver');
        $response->assertViewHas('debts', fn ($v) => $v->count() === 1 && $v->first()->is($matching));
    }

    public function test_index_shows_the_supplier_tractor_driver_name(): void
    {
        $this->unpaidSupplierDebt(['fullname' => 'Client With Supplier']);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index'));

        $response->assertSeeText('Supplier Driver One');
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('debt-supplier.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_is_accessible_to_any_authenticated_user_no_ownership_or_role_check(): void
    {
        $otherUser = User::factory()->create(['email' => 'other-supplier-user@example.com']);
        $this->unpaidSupplierDebt(['fullname' => 'Owned By First User']);

        $response = $this->actingAs($otherUser)->get(route('debt-supplier.index'));

        $response->assertOk();
        $response->assertSeeText('Owned By First User');
    }

    // ------------------------------------------------------------------
    // indexPaid() — paid debts for any driver that is NOT the "normal" one
    // ------------------------------------------------------------------

    public function test_index_paid_renders_empty_state_when_no_paid_supplier_debts_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('debt-supplier.index-paid'));

        $response->assertOk();
        $response->assertViewIs('content.DebtWithSupplier.indexPaid');
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 0);
    }

    public function test_index_paid_renders_a_single_paid_supplier_debt_with_its_products(): void
    {
        $debt = $this->paidSupplierDebt([
            'fullname' => 'Nadia Payer',
            'total_debt_amount' => 300,
            'debt_paid' => 300,
            'rest_debt_amount' => 0,
        ]);

        $category = Category::factory()->create(['name' => 'Gravel']);
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Gravel',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index-paid'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($debts) => $debts->count() === 1);
        $response->assertSeeText('Nadia Payer');
        $response->assertSeeText('Gravel');
    }

    public function test_index_paid_renders_every_matching_paid_supplier_debt_with_no_pagination_today(): void
    {
        Debt::factory()->count(55)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->supplierDriver->id,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index-paid'));

        $response->assertOk();
        $response->assertViewHas('debts', fn ($v) => $v->count() === 55);
    }

    public function test_index_paid_orders_debts_by_id_descending(): void
    {
        $first = $this->paidSupplierDebt(['fullname' => 'First Paid']);
        $second = $this->paidSupplierDebt(['fullname' => 'Second Paid']);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.index-paid'));

        $response->assertViewHas('debts', function ($debts) use ($first, $second) {
            return $debts->first()->is($second) && $debts->last()->is($first);
        });
    }

    public function test_index_paid_requires_authentication(): void
    {
        $response = $this->get(route('debt-supplier.index-paid'));

        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------
    // show() — single debt detail page
    // ------------------------------------------------------------------

    public function test_show_currently_errors_because_getSupplier_relation_does_not_exist(): void
    {
        // KNOWN_ISSUES.md: view.blade.php reads $debt->getSupplier->fullname,
        // but Debt has no getSupplier() relation (only tractorDriver()). In a
        // real request (unlike a bare tinker eval), Laravel's HandleExceptions
        // converts the resulting "Attempt to read property on null" warning
        // into an ErrorException, so debt-supplier.show currently 500s on
        // EVERY view — confirmed via this test, not assumed. Locks in the
        // CURRENT broken behavior; the approved fix in /perf-plan will change
        // this test's expectation once applied.
        $debt = $this->unpaidSupplierDebt(['fullname' => 'Client Sees A 500']);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.show', $debt->id));

        $response->assertStatus(500);
    }

    public function test_show_requires_authentication(): void
    {
        $debt = $this->unpaidSupplierDebt();

        $response = $this->get(route('debt-supplier.show', $debt->id));

        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------
    // edit() — edit form
    // ------------------------------------------------------------------

    public function test_edit_renders_a_debt_with_its_products(): void
    {
        $debt = $this->unpaidSupplierDebt(['fullname' => 'Yacine Client']);

        $category = Category::factory()->create();
        $subcategory = SubCategory::factory()->create(['category_id' => $category->id]);

        DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $subcategory->id,
            'name_category' => 'Iron Bars',
        ]);

        $response = $this->actingAs($this->user)->get(route('debt-supplier.edit', $debt->id));

        $response->assertOk();
        $response->assertViewIs('content.DebtWithSupplier.edit');
        // fullname is rendered into a form <input value="...">, not as text
        // content, so assertSee() (raw HTML) is used instead of assertSeeText().
        $response->assertSee('Yacine Client');
        $response->assertSeeText('Iron Bars');
    }

    public function test_edit_requires_authentication(): void
    {
        $debt = $this->unpaidSupplierDebt();

        $response = $this->get(route('debt-supplier.edit', $debt->id));

        $response->assertRedirect(route('login'));
    }
}
