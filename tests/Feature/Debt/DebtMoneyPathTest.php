<?php

namespace Tests\Feature\Debt;

use App\Models\Debt;
use App\Models\DebtHistory;
use App\Models\DebtProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsDomainFixtures;
use Tests\TestCase;

/**
 * Targets #8-#14: the money paths.
 *
 * Every assertion here pins a real, observed behaviour of DebtController -
 * including several that are plainly bugs. They are encoded as-is so the
 * upgrade cannot change them silently.
 */
class DebtMoneyPathTest extends TestCase
{
    use RefreshDatabase;
    use SeedsDomainFixtures;

    private User $user;
    private $driver;
    private $sub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->appUser();
        $this->driver = $this->normalDriver();
        $this->sub = $this->subCategory();
        $this->actingAs($this->user);
    }

    private function makeDebt(float $total, float $paid = 0.00): Debt
    {
        return Debt::factory()->withTotal($total, $paid)->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->driver->id,
        ]);
    }

    // ---------------------------------------------------------------- #8

    /** Target #8: storing a debt writes the debt, its products, and the totals. */
    public function test_store_creates_debt_with_products_and_derived_totals(): void
    {
        $before = Debt::count();

        $response = $this->from('/debt')->post('/debt', [
            'fullname' => 'Characterization Customer',
            'phone' => '555000111',
            'date_debut_debt' => '2026-01-05',
            'tractor_driver_id' => $this->driver->id,
            'note' => 'a note',
            'name_product' => ['Cement', 'Sand'],
            'quantity' => ['3', '2'],
            'amount_due' => ['750.00', '250.00'],
            'date_debt' => ['2026-01-05', '2026-01-05'],
            'subcategory_ids' => [$this->sub->id, $this->sub->id],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');

        $this->assertSame($before + 1, Debt::count());

        $debt = Debt::latest('id')->first();
        $this->assertSame('Characterization Customer', $debt->fullname);
        $this->assertSame('555000111', $debt->phone);
        $this->assertSame('unpaid', $debt->status);
        $this->assertSame($this->user->id, $debt->user_id);
        $this->assertSame('a note', $debt->note);

        // total is the SUM of amount_due, and rest starts equal to total
        $this->assertEquals(1000.00, $debt->total_debt_amount);
        $this->assertEquals(1000.00, $debt->rest_debt_amount);

        // SMELL: debt_paid is left NULL on create rather than 0.00, so the
        // payDebt arithmetic below starts from NULL and relies on PHP's
        // null-to-zero coercion.
        $this->assertNull($debt->debt_paid);

        $this->assertSame(2, DebtProduct::where('debt_id', $debt->id)->count());
    }

    // ---------------------------------------------------------------- #9

    /** Target #9: invalid input is rejected and writes nothing. */
    public function test_store_rejects_invalid_input_and_writes_nothing(): void
    {
        $before = Debt::count();
        $productsBefore = DebtProduct::count();

        $response = $this->from('/debt')->post('/debt', [
            'fullname' => '',
            'phone' => 'not-numeric',
            'date_debut_debt' => 'not-a-date',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');
        $response->assertSessionHasErrors(['fullname', 'phone', 'date_debut_debt']);

        $this->assertSame($before, Debt::count());
        $this->assertSame($productsBefore, DebtProduct::count());
    }

    // ---------------------------------------------------------------- #10

    /** Target #10: paying the exact total closes the debt and logs history. */
    public function test_exact_payment_marks_debt_paid_and_records_history(): void
    {
        $debt = $this->makeDebt(1000.00);

        $response = $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '1000.00',
            'date_payment' => '2026-01-06 10:00:00',
        ]);

        $response->assertStatus(302);

        $debt->refresh();
        $this->assertSame('paid', $debt->status);
        $this->assertEquals(1000.00, $debt->debt_paid);
        $this->assertEquals(0.00, $debt->rest_debt_amount);
        $this->assertNotNull($debt->date_end_debt);

        $this->assertSame(1, DebtHistory::where('debt_id', $debt->id)->count());
        $this->assertEquals(1000.00, DebtHistory::where('debt_id', $debt->id)->value('amount'));
    }

    // ---------------------------------------------------------------- #11

    /** Target #11: a partial payment leaves the debt open but still stamps an end date. */
    public function test_partial_payment_leaves_debt_unpaid_but_stamps_an_end_date(): void
    {
        $debt = $this->makeDebt(1000.00);

        $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '400.00',
            'date_payment' => '2026-01-06 10:00:00',
        ])->assertStatus(302);

        $debt->refresh();
        $this->assertSame('unpaid', $debt->status);
        $this->assertEquals(400.00, $debt->debt_paid);
        $this->assertEquals(600.00, $debt->rest_debt_amount);

        // SMELL: date_end_debt ("debt closed on") is written even though the
        // debt is still unpaid, so a partially-paid debt looks settled to any
        // report that keys off date_end_debt.
        $this->assertNotNull($debt->date_end_debt);

        $this->assertSame(1, DebtHistory::where('debt_id', $debt->id)->count());
    }

    /** Target #11b: a second partial payment settles the debt. */
    public function test_second_partial_payment_settles_the_debt(): void
    {
        $debt = $this->makeDebt(1000.00);

        $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '400.00', 'date_payment' => '2026-01-06 10:00:00',
        ]);
        $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '600.00', 'date_payment' => '2026-01-07 10:00:00',
        ]);

        $debt->refresh();
        $this->assertSame('paid', $debt->status);
        $this->assertEquals(1000.00, $debt->debt_paid);
        $this->assertEquals(0.00, $debt->rest_debt_amount);
        $this->assertSame(2, DebtHistory::where('debt_id', $debt->id)->count());
    }

    // ---------------------------------------------------------------- #12

    /**
     * Target #12: overpayment is refused - but only AFTER the line items have
     * already been flagged as paid.
     */
    public function test_overpayment_is_refused_but_products_are_already_flagged_paid(): void
    {
        $debt = $this->makeDebt(1000.00);
        $product = DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $this->sub->id,
        ]);

        $response = $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '5000.00',
            'date_payment' => '2026-01-06 10:00:00',
            'id_debt_product' => [$product->id],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');

        $debt->refresh();
        $product->refresh();

        // the debt itself is untouched and nothing is recorded
        $this->assertSame('unpaid', $debt->status);
        $this->assertEquals(0.00, $debt->debt_paid);
        $this->assertEquals(1000.00, $debt->rest_debt_amount);
        $this->assertSame(0, DebtHistory::where('debt_id', $debt->id)->count());

        // SMELL / DATA CORRUPTION: payDebt() flags every id_debt_product as
        // status '1' (paid) at the TOP of the method, before the overpayment
        // guard further down rejects the payment. The early-return leaves the
        // line items marked paid against a debt that received no money at all,
        // and there is no transaction rollback on this path.
        $this->assertSame('1', $product->status);
    }

    // ---------------------------------------------------------------- #13

    /**
     * Target #13: the payment comparisons use loose == on decimal strings.
     *
     * SMELL: DebtController::payDebt() compares with == rather than a decimal
     * comparison, so PHP's numeric-string juggling applies. '1e3' is accepted
     * as equal to 1000.00 and settles the debt in full. Any input that PHP
     * considers numerically equal will close a debt.
     */
    public function test_loose_equality_lets_scientific_notation_settle_a_debt(): void
    {
        $debt = $this->makeDebt(1000.00);

        $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'debt_paid' => '1e3',
            'date_payment' => '2026-01-06 10:00:00',
        ])->assertStatus(302);

        $debt->refresh();
        $this->assertSame('paid', $debt->status);
        $this->assertEquals(1000.00, $debt->debt_paid);
        $this->assertEquals(0.00, $debt->rest_debt_amount);
    }

    /**
     * Target #13b: payDebt has no validation at all.
     *
     * SMELL: unlike store()/update(), payDebt() runs no Validator. Posting no
     * amount produces a NULL history amount, the insert violates the NOT NULL
     * constraint, the catch block swallows the exception and the user is simply
     * redirected back with no payment recorded and no error surfaced.
     */
    public function test_payment_without_an_amount_is_silently_swallowed(): void
    {
        $debt = $this->makeDebt(1000.00);

        $response = $this->from('/debt')->patch('/debt/pays/' . $debt->id, [
            'date_payment' => '2026-01-06 10:00:00',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');

        $debt->refresh();
        $this->assertSame('unpaid', $debt->status);
        $this->assertSame(0, DebtHistory::where('debt_id', $debt->id)->count());
    }

    // ---------------------------------------------------------------- #14

    /** Target #14: deleting a debt soft-deletes it and redirects to the index. */
    public function test_destroy_soft_deletes_the_debt(): void
    {
        $debt = $this->makeDebt(1000.00);

        $response = $this->from('/debt')->delete('/debt/' . $debt->id);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');

        $this->assertSoftDeleted('debts', ['id' => $debt->id]);
        $this->assertSame(0, Debt::count());
    }

    /** Target #14b: updating a debt recomputes the totals from the line items. */
    public function test_update_recomputes_totals_from_line_items(): void
    {
        $debt = $this->makeDebt(1000.00);
        $product = DebtProduct::factory()->create([
            'debt_id' => $debt->id,
            'subcategory_id' => $this->sub->id,
        ]);

        $response = $this->from('/debt')->put('/debt/' . $debt->id, [
            'fullname' => 'Renamed Customer',
            'phone' => '555222333',
            'date_debut_debt' => '2026-01-05',
            'id' => [$product->id],
            'name_product' => ['Gravel'],
            'quantity' => ['5'],
            'amount_due' => ['1250.00'],
            'date_debt' => ['2026-01-05'],
            'subcategory_ids' => [$this->sub->id],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/debt');

        $debt->refresh();
        $this->assertSame('Renamed Customer', $debt->fullname);
        $this->assertEquals(1250.00, $debt->total_debt_amount);
        $this->assertSame('unpaid', $debt->status);

        $this->assertSame(1, DebtProduct::where('debt_id', $debt->id)->count());
        $this->assertEquals(1250.00, DebtProduct::where('debt_id', $debt->id)->value('amount'));
    }

    /** Target #14c: the AJAX name search returns matching debtors as JSON. */
    public function test_debt_name_search_returns_json_matches(): void
    {
        $this->makeDebt(1000.00);
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $this->driver->id,
            'fullname' => 'Searchable Person',
            'phone' => '0555123456',
        ]);

        $response = $this->postJson('/debt/search', ['query' => 'Searchable']);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJsonFragment([
            'fullname' => 'Searchable Person',
            'phone' => '0555123456',
        ]);
    }
}
