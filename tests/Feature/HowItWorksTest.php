<?php

namespace Tests\Feature;

use Tests\TestCase;

class HowItWorksTest extends TestCase
{
    public function test_home_page_shows_how_it_works_section(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-labelledby="how-heading"', false);
        $response->assertSee(__('home.how_heading'));
    }

    public function test_how_it_works_steps_are_an_ordered_list_in_order(): void
    {
        $this->get(route('home'))->assertSeeInOrder([
            '<ol class="steps">',
            '<li>'.e(__('home.how_step_search')).'</li>',
            '<li>'.e(__('home.how_step_check')).'</li>',
            '<li>'.e(__('home.how_step_contact')).'</li>',
            '</ol>',
        ], false);
    }
}
