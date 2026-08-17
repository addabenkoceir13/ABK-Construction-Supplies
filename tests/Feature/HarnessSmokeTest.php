<?php

namespace Tests\Feature;

use Tests\TestCase;

class HarnessSmokeTest extends TestCase
{
    public function test_suite_is_pointed_at_the_dedicated_test_schema(): void
    {
        $connection = config('database.default');

        $this->assertSame('mysql_testing', $connection);
        $this->assertSame(
            'dettessofiane_test',
            config("database.connections.{$connection}.database")
        );
        $this->assertNotSame(
            'dettessofiane',
            config("database.connections.{$connection}.database")
        );
    }
}
