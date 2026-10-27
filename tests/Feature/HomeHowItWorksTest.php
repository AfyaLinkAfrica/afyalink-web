<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeHowItWorksTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_explains_how_it_works_in_three_steps(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('home.how_heading'))
            ->assertSeeInOrder([
                __('home.how_step_1'),
                __('home.how_step_2'),
                __('home.how_step_3'),
            ]);
    }

    public function test_the_logo_declares_its_size_to_avoid_layout_shift(): void
    {
        $this->get(route('home'))
            ->assertSee('width="400" height="400"', false);
    }

    public function test_the_logo_file_is_small_enough_for_slow_connections(): void
    {
        $this->assertLessThan(100 * 1024, filesize(public_path('images/logo.png')));
    }
}
