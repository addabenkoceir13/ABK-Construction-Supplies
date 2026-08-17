<?php

namespace Tests\Feature\Read;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsDomainFixtures;
use Tests\TestCase;

/**
 * Target #20: routes that were reachable WITHOUT authentication.
 *
 * FIXED (pre-hop-1 security PR): /list/debt/supplier/ and /password/hash
 * were unauthenticated closure routes in routes/web.php - the former leaked
 * debtor PII (full name, phone, balances) to any anonymous visitor, the
 * latter was a public bcrypt oracle. Neither had any internal caller
 * anywhere in the codebase (routes, views, JS), so both were deleted
 * outright rather than gated behind auth - confirmed with the user first.
 * This is the one case where a characterization test is deliberately
 * updated rather than left pinned: the insecure behaviour it protected
 * no longer exists, so pinning "still 200 and leaks PII" would be pinning
 * a bug that was just fixed. See docs/upgrade/01-SAFETY-NET.md.
 */
class PublicExposureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsDomainFixtures;

    /** Target #20: /list/debt/supplier/ no longer exists - route removed entirely. */
    public function test_supplier_debt_list_route_no_longer_exists(): void
    {
        $this->assertGuest();
        $response = $this->get('/list/debt/supplier/');

        $response->assertStatus(404);
    }

    /** Target #20d: /password/hash no longer exists - route removed entirely. */
    public function test_password_hash_debug_route_no_longer_exists(): void
    {
        $this->assertGuest();
        $response = $this->get('/password/hash');

        $response->assertStatus(404);
    }
}
