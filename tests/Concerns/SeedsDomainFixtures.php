<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\TractorDriver;
use App\Models\User;
use App\Models\Vehicle;

/**
 * Minimum fixtures shared by the characterization suite.
 */
trait SeedsDomainFixtures
{
    /**
     * Create the "normal" tractor driver pinned to id 1.
     *
     * DebtRepository::debtUnPaid()/debtPaid() and the public
     * /list/debt/supplier/ route are all hardcoded to the literal id 1, so the
     * fixture must occupy that exact row.
     *
     * The id is set explicitly rather than relying on insertion order: under
     * RefreshDatabase each test is rolled back inside a transaction, and InnoDB
     * does NOT rewind its auto-increment counter on rollback. So the "first"
     * driver created in the second and later tests of a run would otherwise get
     * id 2, 3, ... and silently stop matching the hardcoded queries.
     */
    protected function normalDriver(): TractorDriver
    {
        return TractorDriver::factory()->normal()->create(['id' => 1]);
    }

    /** A supplier-side driver, guaranteed NOT to be id 1. */
    protected function deliveryDriver(): TractorDriver
    {
        return TractorDriver::factory()->create(['id' => 2, 'type' => 'delivery']);
    }

    protected function subCategory(): SubCategory
    {
        return SubCategory::factory()->create([
            'category_id' => Category::factory()->create()->id,
        ]);
    }

    protected function vehicle(): Vehicle
    {
        return Vehicle::factory()->create();
    }

    protected function appUser(): User
    {
        return User::factory()->create();
    }
}
