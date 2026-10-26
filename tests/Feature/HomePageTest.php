<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_project_name_and_disclaimer(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('AfyaLink Africa');
        $response->assertSee('does not replace professional medical advice');
    }
}
