<?php

namespace Tests\Feature\Read;

use App\Models\Debt;
use App\Models\FuelStation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsDomainFixtures;
use Tests\TestCase;

/**
 * The three most-visited read pages: the dashboard, the debt index and the
 * fuel-station index.
 */
class DashboardReadTest extends TestCase
{
    use RefreshDatabase;
    use SeedsDomainFixtures;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->appUser();
        $this->actingAs($this->user);
    }

    /**
     * The dashboard 500s on a completely empty database.
     *
     * SMELL: resources/views/content/dashboard/index2.blade.php computes
     * percentage shares by dividing by the totals it is given. With no debts
     * and no fuel receipts every total is 0 and the view raises
     * "Division by zero" (a fatal DivisionByZeroError in PHP 8, where PHP 7
     * would merely have warned). A brand-new install therefore cannot render
     * its own home page.
     *
     * This is pinned deliberately: PHP's division semantics are exactly the
     * kind of thing an upgrade can shift, and we want to be told if it does.
     */
    public function test_dashboard_errors_on_an_empty_database(): void
    {
        $this->assertSame(0, Debt::count());
        $this->assertSame(0, FuelStation::count());

        $this->get('/')->assertStatus(500);
    }

    /** With data present the dashboard renders. */
    public function test_dashboard_renders_once_any_data_exists(): void
    {
        $driver = $this->normalDriver();
        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $driver->id,
        ]);
        FuelStation::factory()->create(['vehicle_id' => $this->vehicle()->id]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** The debt index renders and lists debts attached to driver id 1. */
    public function test_debt_index_lists_debts_for_driver_one(): void
    {
        $driver = $this->normalDriver();
        $this->assertSame(1, $driver->id, 'the normal driver must occupy id 1');

        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $driver->id,
            'fullname' => 'Indexed Customer',
            'status' => 'unpaid',
        ]);

        $response = $this->get('/debt');

        $response->assertStatus(200);
        $response->assertSee('الديون', false);
        $response->assertSee('Indexed Customer', false);
    }

    /** The paid-debt index excludes unpaid debts. */
    public function test_paid_debt_index_excludes_unpaid_debts(): void
    {
        $driver = $this->normalDriver();

        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $driver->id,
            'fullname' => 'Still Owing Person',
            'status' => 'unpaid',
        ]);

        $response = $this->get('/debt/status/paid');

        $response->assertStatus(200);
        $response->assertDontSee('Still Owing Person', false);
    }

    /** The fuel-station index renders and lists receipts. */
    public function test_fuel_station_index_lists_receipts(): void
    {
        FuelStation::factory()->create([
            'vehicle_id' => $this->vehicle()->id,
            'name_owner' => 'Listed Owner',
        ]);

        $response = $this->get('/fuel-stations');

        $response->assertStatus(200);
        $response->assertSee('محاسبة مشتريات الوقود', false);
        $response->assertSee('Listed Owner', false);
    }

    /** The supplier-debt index renders for authenticated users. */
    public function test_supplier_debt_index_renders(): void
    {
        $this->normalDriver();
        $supplier = $this->deliveryDriver();

        Debt::factory()->create([
            'user_id' => $this->user->id,
            'tractor_driver_id' => $supplier->id,
            'fullname' => 'Supplier Side Debtor',
            'status' => 'unpaid',
        ]);

        $response = $this->get('/debt-supplier');

        $response->assertStatus(200);
        $response->assertSee('Supplier Side Debtor', false);
    }

    /** The service listing pages render for authenticated users. */
    public function test_service_index_pages_render(): void
    {
        $this->get('/services/building-materals')->assertStatus(200);
        $this->get('/services/tractor-driver')->assertStatus(200);
        $this->get('/services/vehicle')->assertStatus(200);
    }
}
