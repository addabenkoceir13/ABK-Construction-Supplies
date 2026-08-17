<?php

namespace Tests\Feature\Read;

use App\Models\Debt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsDomainFixtures;
use Tests\TestCase;

/**
 * Target #20: routes that are reachable WITHOUT authentication.
 *
 * Both routes below are declared in routes/web.php outside the
 * Route::group(['middleware' => ['auth']]) block. These tests deliberately
 * characterize the current, insecure behaviour so the upgrade cannot change it
 * by accident. They are NOT an endorsement of it - see docs/upgrade/01-SAFETY-NET.md.
 */
class PublicExposureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsDomainFixtures;

    /**
     * Target #20: /list/debt/supplier/ leaks unpaid supplier debts to anyone.
     *
     * SMELL: this closure route has no 'auth' middleware. It renders every
     * unpaid debt for tractor_driver_id != 1, exposing debtor full names,
     * phone numbers and outstanding balances to unauthenticated visitors.
     */
    public function test_supplier_debt_list_is_publicly_readable_and_leaks_debtor_pii(): void
    {
        $user = $this->appUser();
        $this->normalDriver();               // occupies id 1
        $supplierDriver = $this->deliveryDriver();

        Debt::factory()->create([
            'user_id' => $user->id,
            'tractor_driver_id' => $supplierDriver->id,
            'fullname' => 'Leaked Debtor Name',
            'phone' => '0555999888',
            'status' => 'unpaid',
        ]);

        // no actingAs(): this is an anonymous visitor
        $this->assertGuest();
        $response = $this->get('/list/debt/supplier/');

        $response->assertStatus(200);

        // PII is rendered to an unauthenticated visitor
        $response->assertSee('Leaked Debtor Name', false);
        $response->assertSee('0555999888', false);
    }

    /** Target #20b: paid supplier debts are excluded from the public list. */
    public function test_public_supplier_list_excludes_paid_debts(): void
    {
        $user = $this->appUser();
        $this->normalDriver();
        $supplierDriver = $this->deliveryDriver();

        Debt::factory()->paid()->create([
            'user_id' => $user->id,
            'tractor_driver_id' => $supplierDriver->id,
            'fullname' => 'Already Settled Person',
        ]);

        $response = $this->get('/list/debt/supplier/');

        $response->assertStatus(200);
        $response->assertDontSee('Already Settled Person', false);
    }

    /** Target #20c: debts belonging to driver id 1 are excluded from the public list. */
    public function test_public_supplier_list_excludes_the_normal_driver(): void
    {
        $user = $this->appUser();
        $normal = $this->normalDriver();

        Debt::factory()->create([
            'user_id' => $user->id,
            'tractor_driver_id' => $normal->id,
            'fullname' => 'Walk In Customer',
            'status' => 'unpaid',
        ]);

        $response = $this->get('/list/debt/supplier/');

        $response->assertStatus(200);
        $response->assertDontSee('Walk In Customer', false);
    }

    /**
     * Target #20d: /password/hash is a public bcrypt oracle.
     *
     * SMELL: this debug route is unauthenticated and returns a bcrypt hash of
     * the hardcoded password '123456789' as the raw response body. It is dead
     * developer scaffolding that shipped to the app's route table.
     */
    public function test_password_hash_debug_route_is_public(): void
    {
        $this->assertGuest();

        $response = $this->get('/password/hash');

        $response->assertStatus(200);
        $this->assertMatchesRegularExpression('/^\$2y\$/', trim($response->getContent()));
    }
}
