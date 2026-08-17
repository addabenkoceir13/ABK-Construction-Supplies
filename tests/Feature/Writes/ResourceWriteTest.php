<?php

namespace Tests\Feature\Writes;

use App\Models\Category;
use App\Models\FuelStation;
use App\Models\InsuranceVehicle;
use App\Models\TractorDriver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsDomainFixtures;
use Tests\TestCase;

/**
 * Targets #15-#19: the remaining DB-write routes.
 */
class ResourceWriteTest extends TestCase
{
    use RefreshDatabase;
    use SeedsDomainFixtures;

    /** A referer that is deliberately NOT any real route, so that a
     *  redirect()->back() is distinguishable from a redirect()->route(). */
    private const REFERER = '/distinct-referer';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->appUser();
        $this->actingAs($this->user);
    }

    // ---------------------------------------------------------------- #15

    /** Target #15: a fuel receipt is created with the posted columns. */
    public function test_fuel_station_store_creates_receipt(): void
    {
        $vehicle = $this->vehicle();
        $before = FuelStation::count();

        $response = $this->from(self::REFERER)->post('/fuel-stations', [
            'vehicle_id' => $vehicle->id,
            'name_owner' => 'Owner Name',
            'name_driver' => 'Driver Name',
            'name_distributor' => 'Distributor Name',
            'filing_datetime' => '2026-01-05 08:00:00',
            'liter' => '50',
            'amount' => '2500',
            'type_fuel' => 'diesel',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);

        $this->assertSame($before + 1, FuelStation::count());

        $fuel = FuelStation::latest('id')->first();
        $this->assertSame($vehicle->id, $fuel->vehicle_id);
        $this->assertSame('Owner Name', $fuel->name_owner);
        $this->assertSame('Driver Name', $fuel->name_driver);
        $this->assertSame('Distributor Name', $fuel->name_distributor);
        $this->assertSame('diesel', $fuel->type_fuel);
        $this->assertEquals(2500, $fuel->amount);
        $this->assertEquals(50, $fuel->liter);

        // new receipts always start unpaid (column default)
        $this->assertSame('unpaid', $fuel->status);
    }

    /** Target #15b: fuel receipt validation rejects a bad vehicle and writes nothing. */
    public function test_fuel_station_store_rejects_unknown_vehicle(): void
    {
        $before = FuelStation::count();

        $response = $this->from(self::REFERER)->post('/fuel-stations', [
            'vehicle_id' => 999999,
            'name_owner' => 'Owner Name',
            'name_driver' => 'Driver Name',
            'name_distributor' => 'Distributor Name',
            'filing_datetime' => '2026-01-05 08:00:00',
            'amount' => '2500',
            'type_fuel' => 'diesel',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['vehicle_id']);
        $this->assertSame($before, FuelStation::count());
    }

    // ---------------------------------------------------------------- #16

    /** Target #16: the bulk "mark as paid" action flips status for the given ids. */
    public function test_fuel_station_bulk_status_marks_receipts_paid(): void
    {
        $vehicle = $this->vehicle();
        $target = FuelStation::factory()->create(['vehicle_id' => $vehicle->id]);
        $untouched = FuelStation::factory()->create(['vehicle_id' => $vehicle->id]);

        $response = $this->from(self::REFERER)->post('/fuel-stations/change-status', [
            'ids' => [$target->id],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);

        $this->assertSame('paid', $target->fresh()->status);
        $this->assertSame('unpaid', $untouched->fresh()->status);
    }

    /** Target #16b: the single-receipt status route sets the posted status. */
    public function test_fuel_station_single_status_update(): void
    {
        $fuel = FuelStation::factory()->create(['vehicle_id' => $this->vehicle()->id]);

        $response = $this->from(self::REFERER)->patch('/fuel-stations/status/' . $fuel->id, [
            'status' => 'paid',
        ]);

        $response->assertStatus(302);
        $this->assertSame('paid', $fuel->fresh()->status);
    }

    /** Target #16c: deleting a fuel receipt soft-deletes it. */
    public function test_fuel_station_destroy_soft_deletes(): void
    {
        $fuel = FuelStation::factory()->create(['vehicle_id' => $this->vehicle()->id]);

        $this->from(self::REFERER)->delete('/fuel-stations/' . $fuel->id)->assertStatus(302);

        $this->assertSoftDeleted('fuel_stations', ['id' => $fuel->id]);
    }

    // ---------------------------------------------------------------- #17

    /**
     * Target #17: creating a building-materials category.
     *
     * SMELL: CategoryController::store() succeeds at the DB write and then
     * calls redirect()->route('building-materals.index'). No such route name
     * exists - the resource is registered with ->names('services.building-materials'),
     * so route() throws RouteNotFoundException. The surrounding try/catch
     * swallows it and issues redirect()->back() with an error toast instead.
     * Net effect: the row IS created but the user is told it failed.
     */
    public function test_category_store_creates_row_but_redirects_back_due_to_bad_route_name(): void
    {
        $before = Category::count();

        $response = $this->from(self::REFERER)->post('/services/building-materals', [
            'name' => 'Reinforced Concrete',
        ]);

        $response->assertStatus(302);

        // the row is written ...
        $this->assertSame($before + 1, Category::count());
        $this->assertDatabaseHas('categories', ['name' => 'Reinforced Concrete']);

        // ... but the redirect lands back on the referer, NOT on the index route,
        // proving the route() call blew up and was swallowed.
        $response->assertRedirect(self::REFERER);
    }

    /** Target #17b: category validation rejects an empty name and writes nothing. */
    public function test_category_store_rejects_empty_name(): void
    {
        $before = Category::count();

        $response = $this->from(self::REFERER)->post('/services/building-materals', ['name' => '']);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $this->assertSame($before, Category::count());
    }

    /** Target #17c: updating a category writes the new name (same swallowed redirect). */
    public function test_category_update_renames_row(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->from(self::REFERER)->put('/services/building-materals/' . $category->id, [
            'name' => 'New Name',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);
        $this->assertSame('New Name', $category->fresh()->name);
    }

    // ---------------------------------------------------------------- #18

    /**
     * Target #18: creating a tractor driver.
     *
     * SMELL: TractorDriverController::store() validates only fullname+phone but
     * then persists $request->all(). Because `type` and `status` are in the
     * model's $fillable, a client can set them to any enum value - including
     * type=normal, which is the flag the debt listing keys off.
     */
    public function test_tractor_driver_store_mass_assigns_unvalidated_type_and_status(): void
    {
        $before = TractorDriver::count();

        $response = $this->from(self::REFERER)->post('/services/tractor-driver', [
            'fullname' => 'Injected Driver',
            'phone' => '600111222',
            'type' => 'normal',
            'status' => 'blocked',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);

        $this->assertSame($before + 1, TractorDriver::count());

        $driver = TractorDriver::latest('id')->first();
        $this->assertSame('Injected Driver', $driver->fullname);
        $this->assertSame('normal', $driver->type);
        $this->assertSame('blocked', $driver->status);
    }

    /** Target #18b: tractor driver validation rejects a non-numeric phone. */
    public function test_tractor_driver_store_rejects_non_numeric_phone(): void
    {
        $before = TractorDriver::count();

        $response = $this->from(self::REFERER)->post('/services/tractor-driver', [
            'fullname' => 'Bad Phone',
            'phone' => 'not-a-number',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['phone']);
        $this->assertSame($before, TractorDriver::count());
    }

    /** Target #18c: deleting a tractor driver soft-deletes it. */
    public function test_tractor_driver_destroy_soft_deletes(): void
    {
        $driver = TractorDriver::factory()->create();

        $this->from(self::REFERER)->delete('/services/tractor-driver/' . $driver->id)
            ->assertStatus(302);

        $this->assertSoftDeleted('tractor_drivers', ['id' => $driver->id]);
    }

    // ---------------------------------------------------------------- #19

    /** Target #19: creating a vehicle also creates its insurance row and composes the plate. */
    public function test_vehicle_store_creates_vehicle_and_insurance_row(): void
    {
        $beforeVehicles = Vehicle::count();
        $beforeInsurance = InsuranceVehicle::count();

        $response = $this->from(self::REFERER)->post('/services/vehicle', [
            'name' => 'Site Truck',
            'type' => 'truck',
            'wilaya_license' => '16',
            'year_license' => '2020',
            'license' => '12345',
            'start_date' => '2026-01-01',
            'end_date' => '2027-01-01',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);

        $this->assertSame($beforeVehicles + 1, Vehicle::count());
        $this->assertSame($beforeInsurance + 1, InsuranceVehicle::count());

        $vehicle = Vehicle::latest('id')->first();
        $this->assertSame('Site Truck', $vehicle->name);
        $this->assertSame('truck', $vehicle->type);

        // license_plate is composed as "<license> - <year> - <wilaya>"
        $this->assertSame('12345 - 2020 - 16', $vehicle->license_plate);

        $this->assertSame(1, InsuranceVehicle::where('vehicle_id', $vehicle->id)->count());
    }

    /** Target #19b: the add-insurance-date route appends another insurance row. */
    public function test_vehicle_add_insurance_date_appends_row(): void
    {
        $vehicle = $this->vehicle();
        InsuranceVehicle::factory()->create(['vehicle_id' => $vehicle->id]);
        $before = InsuranceVehicle::where('vehicle_id', $vehicle->id)->count();

        $response = $this->from(self::REFERER)->post('/services/vehicle/' . $vehicle->id . '/added-date', [
            'start_date' => '2026-02-01',
            'end_date' => '2027-02-01',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::REFERER);

        $this->assertSame(
            $before + 1,
            InsuranceVehicle::where('vehicle_id', $vehicle->id)->count()
        );
        $this->assertDatabaseHas('insurance_vehicles', [
            'vehicle_id' => $vehicle->id,
            'start_date' => '2026-02-01',
            'end_date' => '2027-02-01',
        ]);
    }

    /** Target #19c: add-insurance-date validation rejects missing dates. */
    public function test_vehicle_add_insurance_date_requires_both_dates(): void
    {
        $vehicle = $this->vehicle();
        $before = InsuranceVehicle::count();

        $response = $this->from(self::REFERER)->post('/services/vehicle/' . $vehicle->id . '/added-date', [
            'start_date' => '2026-02-01',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['end_date']);
        $this->assertSame($before, InsuranceVehicle::count());
    }

    /** Target #19d: deleting a vehicle soft-deletes it. */
    public function test_vehicle_destroy_soft_deletes(): void
    {
        $vehicle = $this->vehicle();

        $this->from(self::REFERER)->delete('/services/vehicle/' . $vehicle->id)
            ->assertStatus(302);

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}
