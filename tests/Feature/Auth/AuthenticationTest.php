<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Targets #1-#7: authentication and session establishment.
 *
 * These are the highest blast-radius routes in the app: every other protected
 * route depends on the session contract asserted here surviving the L9 -> L13
 * upgrade.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private const LOGIN_URL = '/auth/login-basic';
    private const LOGIN_ACTION = '/auth/login-action';

    // ---------------------------------------------------------------- #1

    /** Target #1: a valid POST establishes an authenticated session. */
    public function test_login_with_valid_credentials_establishes_session(): void
    {
        $user = User::factory()->create();

        $response = $this->post(self::LOGIN_ACTION, [
            'email' => $user->email,
            'password' => UserFactory::PASSWORD,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);

        // The auth guard's session key is the actual contract that keeps the
        // user logged in across requests; assert it directly rather than
        // trusting only the helper.
        $this->assertNotNull(session()->get('login_web_' . sha1(\Illuminate\Auth\SessionGuard::class)));

        // NOTE: LoginBasic::login() also calls ->withSuccess('Signed in'), but
        // that flash value is NOT readable afterwards under the test session
        // driver (array) - only the '_flash.old' marker survives. Since the app
        // runs SESSION_DRIVER=file, asserting on it here would be asserting a
        // test-environment artifact, so it is deliberately left uncovered.
    }

    /**
     * Target #1b: the session identifier is rotated on login.
     *
     * The controller never calls session()->regenerate() itself - this is done
     * by Illuminate's SessionGuard::updateSession(). Pinning it here means that
     * if the upgrade ever changes that guard behaviour we find out immediately,
     * because the app has no fixation protection of its own to fall back on.
     */
    public function test_login_rotates_the_session_id(): void
    {
        $user = User::factory()->create();

        $this->get(self::LOGIN_URL);
        $before = session()->getId();

        $this->post(self::LOGIN_ACTION, [
            'email' => $user->email,
            'password' => UserFactory::PASSWORD,
        ]);

        $this->assertNotSame($before, session()->getId());
    }

    // ---------------------------------------------------------------- #2

    /** Target #2: authenticated state survives the redirect to the dashboard. */
    public function test_authenticated_state_survives_the_redirect(): void
    {
        $user = User::factory()->create();

        // The dashboard divides by zero on a totally empty database
        // (see DashboardReadTest), so give it one debt to chew on.
        \App\Models\Debt::factory()->create([
            'user_id' => $user->id,
            'tractor_driver_id' => \App\Models\TractorDriver::factory()->normal()->create()->id,
        ]);

        $response = $this->post(self::LOGIN_ACTION, [
            'email' => $user->email,
            'password' => UserFactory::PASSWORD,
        ])->assertRedirect('/');

        $followed = $this->get('/');

        $followed->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    // ---------------------------------------------------------------- #3

    /** Target #3: a wrong password does not authenticate. */
    public function test_wrong_password_does_not_authenticate(): void
    {
        $user = User::factory()->create();

        $response = $this->from(self::LOGIN_URL)->post(self::LOGIN_ACTION, [
            'email' => $user->email,
            'password' => 'definitely-not-the-password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::LOGIN_URL);
        $this->assertGuest();

        // SMELL: the failure path reports a FAILURE via ->withSuccess(), i.e. a
        // rejected login flashes the 'success' key with the text
        // 'Login details are not valid'. Not asserted on: see the note in
        // test_login_with_valid_credentials_establishes_session about the flash
        // value being unreadable under the array session driver.
        $this->assertNull(session()->get('login_web_' . sha1(\Illuminate\Auth\SessionGuard::class)));
    }

    /** Target #3b: an unknown email is rejected the same way. */
    public function test_unknown_email_does_not_authenticate(): void
    {
        $response = $this->from(self::LOGIN_URL)->post(self::LOGIN_ACTION, [
            'email' => 'nobody@example.test',
            'password' => 'whatever123',
        ]);

        $response->assertRedirect(self::LOGIN_URL);
        $this->assertGuest();
    }

    // ---------------------------------------------------------------- #4

    /** Target #4: malformed input is rejected by validation before auth runs. */
    public function test_login_validation_rejects_malformed_input(): void
    {
        $response = $this->from(self::LOGIN_URL)->post(self::LOGIN_ACTION, [
            'email' => 'not-an-email',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(self::LOGIN_URL);
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /** Target #4b: a completely empty submission is rejected. */
    public function test_login_validation_rejects_empty_submission(): void
    {
        $response = $this->from(self::LOGIN_URL)->post(self::LOGIN_ACTION, []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    // ---------------------------------------------------------------- #5

    /** Target #5: logout clears the whole session, not just the auth keys. */
    public function test_logout_clears_the_entire_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        session(['theme' => 'dark', 'unrelated_key' => 'should-survive-but-does-not']);

        $response = $this->get('/auth/logout');

        $response->assertStatus(302);
        $response->assertRedirect(self::LOGIN_URL);
        $this->assertGuest();

        // SMELL: LogoutBasic calls Session::flush() BEFORE Auth::logout(), so
        // every unrelated session key is destroyed too - including 'theme' and
        // 'locale', which routes/web.php sets at route-registration time.
        $this->assertNull(session('theme'));
        $this->assertNull(session('unrelated_key'));
    }

    /**
     * Target #5b: logout is a GET route with no CSRF protection.
     *
     * SMELL: /auth/logout is registered as GET, so it carries no CSRF token and
     * can be triggered cross-site (e.g. an <img src> tag) to forcibly log a
     * user out. Pinned as-is; this test documents the hole rather than fixing it.
     */
    public function test_logout_succeeds_over_get_without_any_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/auth/logout')->assertRedirect(self::LOGIN_URL);

        $this->assertGuest();
    }

    // ---------------------------------------------------------------- #6

    /**
     * Target #6: guests are bounced off protected routes by the 'auth' alias.
     *
     * This is the breakage-detection canary: commenting out the 'auth' entry in
     * App\Http\Kernel::$routeMiddleware makes this test fail.
     *
     * @dataProvider protectedRoutes
     */
    public function test_guest_is_redirected_from_protected_routes(string $method, string $uri): void
    {
        $response = $this->call($method, $uri);

        $response->assertStatus(302);
        $response->assertRedirect(self::LOGIN_URL);
        $this->assertGuest();
    }

    public static function protectedRoutes(): array
    {
        return [
            'dashboard'        => ['GET', '/'],
            'template'         => ['GET', '/template'],
            'debt index'       => ['GET', '/debt'],
            'debt paid index'  => ['GET', '/debt/status/paid'],
            'debt supplier'    => ['GET', '/debt-supplier'],
            'fuel stations'    => ['GET', '/fuel-stations'],
            'categories'       => ['GET', '/services/building-materals'],
            'tractor drivers'  => ['GET', '/services/tractor-driver'],
            'vehicles'         => ['GET', '/services/vehicle'],
            'debt store'       => ['POST', '/debt'],
            'fuel store'       => ['POST', '/fuel-stations'],
        ];
    }

    /** Target #6b: the login page itself stays reachable for guests. */
    public function test_guest_can_reach_the_login_page(): void
    {
        $response = $this->get(self::LOGIN_URL);

        $response->assertStatus(200);
        $response->assertSee('تسجيل الدخول الأساسي', false);
    }

    // ---------------------------------------------------------------- #7

    /**
     * Target #7: the registration endpoint is BROKEN and always 500s.
     *
     * SMELL: RegisterBasic::register() validates 'username' => 'unique:users',
     * but the users table has no `username` column (it is `name`). The unique
     * rule therefore issues `select count(*) ... where username = ?` and MySQL
     * rejects it with "Unknown column 'username'". No user is ever created.
     *
     * Pinned as-is: this route is dead today and must stay dead-in-the-same-way
     * through the upgrade, or be fixed deliberately as its own change.
     */
    public function test_registration_is_broken_and_creates_no_user(): void
    {
        $before = User::count();

        $response = $this->post('/auth/register-action', [
            'username' => 'brandnewuser',
            'email' => 'brandnewuser@example.test',
            'password' => 'secret123',
        ]);

        $response->assertStatus(500);
        $this->assertSame($before, User::count());
        $this->assertGuest();
    }

    /** Target #7b: the registration form itself still renders. */
    public function test_registration_page_renders_for_guests(): void
    {
        $this->get('/auth/register-basic')->assertStatus(200);
    }
}
