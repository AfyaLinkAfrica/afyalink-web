<?php

namespace Tests\Feature;

use Tests\TestCase;

class LayoutTest extends TestCase
{
    public function test_home_page_uses_the_shared_layout(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('<main id="main-content">', false);
        $response->assertSee(__('layout.emergency'));
        $response->assertSee(__('layout.disclaimer'));
    }

    public function test_facilities_page_uses_the_shared_layout(): void
    {
        $response = $this->get(route('facilities.index'));

        $response->assertOk();
        $response->assertSee(__('facilities.heading'));
        $response->assertSee('<main id="main-content">', false);
        $response->assertSee(__('layout.emergency'));
        $response->assertSee(__('layout.disclaimer'));
    }

    public function test_navigation_marks_the_current_page(): void
    {
        $this->get(route('facilities.index'))
            ->assertSee('aria-current="page"', false);
    }
}
