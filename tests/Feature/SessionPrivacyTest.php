<?php

namespace Tests\Feature;

use Tests\TestCase;

class SessionPrivacyTest extends TestCase
{
    public function test_sessions_are_not_stored_in_the_database_by_default(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('SESSION_DRIVER=file', $example);
        $this->assertStringNotContainsString('SESSION_DRIVER=database', $example);
    }

    public function test_the_fallback_session_driver_does_not_store_visitor_addresses(): void
    {
        $this->assertStringContainsString("env('SESSION_DRIVER', 'file')", file_get_contents(config_path('session.php')));
    }
}
