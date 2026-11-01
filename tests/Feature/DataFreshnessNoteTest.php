<?php

namespace Tests\Feature;

use Tests\TestCase;

class DataFreshnessNoteTest extends TestCase
{
    public function test_home_page_shows_data_freshness_note(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-labelledby="freshness-heading"', false);
        $response->assertSee(__('home.freshness_heading'));
        $response->assertSee(__('home.freshness_note'));
    }
}
