<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Schema names the test suite is never allowed to touch.
     *
     * RefreshDatabase runs migrations and truncates tables. If the suite ever
     * boots against the development or production schema it will destroy real
     * data, so we fail loudly before a single test runs.
     */
    private const FORBIDDEN_DATABASES = [
        'dettessofiane',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->guardAgainstNonTestDatabase();
    }

    private function guardAgainstNonTestDatabase(): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if (in_array($database, self::FORBIDDEN_DATABASES, true)) {
            $this->fail(sprintf(
                'REFUSING TO RUN: tests are pointed at the protected database "%s" '
                . '(connection "%s"). Check phpunit.xml / DB_TEST_DATABASE.',
                $database,
                $connection
            ));
        }

        if (! is_string($database) || ! str_ends_with($database, '_test')) {
            $this->fail(sprintf(
                'REFUSING TO RUN: the test database name "%s" (connection "%s") does not '
                . 'end in "_test". Refusing to migrate an unrecognised schema.',
                var_export($database, true),
                $connection
            ));
        }
    }
}
