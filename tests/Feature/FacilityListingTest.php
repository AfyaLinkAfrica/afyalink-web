<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_shows_facilities_with_their_details(): void
    {
        Facility::factory()->create([
            'name' => 'Sample Hospital Alpha',
            'county' => 'Machakos',
            'type' => 'hospital',
            'phone' => '+254700123456',
        ]);

        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('Sample Hospital Alpha')
            ->assertSee('Machakos')
            ->assertSee('tel:+254700123456', false)
            ->assertSee(__('facilities.types.hospital'));
    }

    public function test_listing_is_paginated_with_15_per_page(): void
    {
        Facility::factory()->count(20)->create();

        $first = $this->get(route('facilities.index'))->assertOk();
        $this->assertSame(15, $first->viewData('facilities')->count());
        $this->assertSame(20, $first->viewData('facilities')->total());

        $second = $this->get(route('facilities.index', ['page' => 2]))->assertOk();
        $this->assertSame(5, $second->viewData('facilities')->count());
    }

    public function test_listing_is_sorted_by_name(): void
    {
        Facility::factory()->create(['name' => 'Zeta Clinic']);
        Facility::factory()->create(['name' => 'Alpha Clinic']);

        $this->get(route('facilities.index'))
            ->assertSeeInOrder(['Alpha Clinic', 'Zeta Clinic']);
    }

    public function test_county_filter_limits_results(): void
    {
        Facility::factory()->create(['name' => 'Nairobi Place One', 'county' => 'Nairobi']);
        Facility::factory()->create(['name' => 'Mombasa Place One', 'county' => 'Mombasa']);

        $this->get(route('facilities.index', ['county' => 'Nairobi']))
            ->assertOk()
            ->assertSee('Nairobi Place One')
            ->assertDontSee('Mombasa Place One');
    }

    public function test_type_filter_limits_results(): void
    {
        Facility::factory()->create(['name' => 'Alpha Dispensary', 'type' => 'dispensary']);
        Facility::factory()->create(['name' => 'Beta Hospital', 'type' => 'hospital']);

        $this->get(route('facilities.index', ['type' => 'dispensary']))
            ->assertSee('Alpha Dispensary')
            ->assertDontSee('Beta Hospital');
    }

    public function test_name_search_matches_part_of_a_name_ignoring_case(): void
    {
        Facility::factory()->create(['name' => 'Machakos Level 5 Hospital']);
        Facility::factory()->create(['name' => 'Unrelated Clinic']);

        $this->get(route('facilities.index', ['q' => 'level 5']))
            ->assertSee('Machakos Level 5 Hospital')
            ->assertDontSee('Unrelated Clinic');
    }

    public function test_filters_can_be_combined(): void
    {
        Facility::factory()->create(['name' => 'Match Me', 'county' => 'Kisumu', 'type' => 'clinic']);
        Facility::factory()->create(['name' => 'Wrong County', 'county' => 'Nairobi', 'type' => 'clinic']);
        Facility::factory()->create(['name' => 'Wrong Type', 'county' => 'Kisumu', 'type' => 'hospital']);

        $this->get(route('facilities.index', ['county' => 'Kisumu', 'type' => 'clinic']))
            ->assertSee('Match Me')
            ->assertDontSee('Wrong County')
            ->assertDontSee('Wrong Type');
    }

    public function test_unknown_filter_values_are_ignored(): void
    {
        Facility::factory()->count(3)->create();

        $response = $this->get(route('facilities.index', ['county' => 'Atlantis', 'type' => 'spaceship']))
            ->assertOk();

        $this->assertSame(3, $response->viewData('facilities')->total());
    }

    public function test_non_text_filter_values_do_not_cause_errors(): void
    {
        $this->get('/facilities?county[]=Nairobi&type[]=clinic&q[]=x')->assertOk();
    }

    public function test_wildcard_characters_in_the_name_search_are_not_special(): void
    {
        Facility::factory()->count(3)->create();

        $response = $this->get(route('facilities.index', ['q' => '%']))->assertOk();

        $this->assertSame(3, $response->viewData('facilities')->total());
    }

    public function test_empty_results_show_a_message_and_a_way_to_clear_the_search(): void
    {
        $this->get(route('facilities.index', ['county' => 'Nairobi']))
            ->assertOk()
            ->assertSee(__('facilities.no_results'))
            ->assertSee(__('facilities.clear_filters'));
    }

    public function test_pagination_links_keep_the_active_filters(): void
    {
        Facility::factory()->count(20)->create(['county' => 'Nairobi']);

        $this->get(route('facilities.index', ['county' => 'Nairobi']))
            ->assertSee('county=Nairobi', false)
            ->assertSee('page=2', false);
    }

    public function test_search_form_keeps_the_selected_values(): void
    {
        $this->get(route('facilities.index', ['county' => 'Kisumu', 'type' => 'hospital', 'q' => 'abc']))
            ->assertSee('<option value="Kisumu" selected>', false)
            ->assertSee('<option value="hospital" selected>', false)
            ->assertSee('value="abc"', false);
    }

    public function test_demo_data_is_clearly_labelled(): void
    {
        Facility::factory()->create(['source' => 'synthetic']);

        $this->get(route('facilities.index'))
            ->assertSee(__('facilities.demo_notice'))
            ->assertSee(__('facilities.demo_badge'));
    }

    public function test_unverified_facilities_are_labelled(): void
    {
        Facility::factory()->unverified()->create();

        $this->get(route('facilities.index'))
            ->assertSee(__('facilities.not_verified'));
    }

    public function test_heading_names_the_county_when_filtered(): void
    {
        $this->get(route('facilities.index', ['county' => 'Nakuru']))
            ->assertSee(__('facilities.in_county', ['county' => 'Nakuru']));
    }
}
