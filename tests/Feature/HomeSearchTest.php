<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_has_an_accessible_search_form(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('role="search"', false);
        $response->assertSee('action="'.route('facilities.index').'"', false);
        $response->assertSee('name="county"', false);
        $response->assertSee('name="type"', false);
        $response->assertSee('for="county"', false);
        $response->assertSee('for="type"', false);
    }

    public function test_county_list_has_47_unique_counties(): void
    {
        $counties = config('afyalink.counties');

        $this->assertCount(47, $counties);
        $this->assertCount(47, array_unique($counties));
    }

    public function test_every_county_appears_in_the_search_form(): void
    {
        $response = $this->get(route('home'));

        foreach (config('afyalink.counties') as $county) {
            $response->assertSee($county);
        }
    }

    public function test_every_facility_type_appears_in_the_search_form(): void
    {
        $response = $this->get(route('home'));

        foreach (Facility::TYPES as $type) {
            $response->assertSee(__('facilities.types.'.$type));
        }
    }

    public function test_featured_counties_are_valid_counties(): void
    {
        foreach (config('afyalink.featured_counties') as $county) {
            $this->assertContains($county, config('afyalink.counties'));
        }
    }

    public function test_county_links_point_to_the_facilities_page(): void
    {
        $this->get(route('home'))
            ->assertSee(route('facilities.index', ['county' => 'Nairobi']), false);
    }

    public function test_facilities_page_accepts_search_parameters(): void
    {
        $this->get(route('facilities.index', ['county' => 'Nairobi', 'type' => 'hospital', 'q' => 'test']))
            ->assertOk();
    }
}
