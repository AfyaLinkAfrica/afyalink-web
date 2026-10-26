<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseDriverTest extends TestCase
{
    /**
     * CI sets EXPECT_DB_DRIVER so the MySQL job proves it really runs on MySQL.
     * Locally the variable is not set, so this test is skipped.
     */
    public function test_tests_run_on_the_expected_database_driver(): void
    {
        $expected = getenv('EXPECT_DB_DRIVER');

        if ($expected === false || $expected === '') {
            $this->markTestSkipped('EXPECT_DB_DRIVER is not set.');
        }

        $this->assertSame($expected, DB::connection()->getDriverName());
    }
}
