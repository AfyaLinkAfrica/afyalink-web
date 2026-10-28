<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityMapTest extends TestCase
{
    use RefreshDatabase;

    private function facilityWithLocation(): Facility
    {
        return Facility::factory()->create(['latitude' => -1.5, 'longitude' => 37.2]);
    }

    public function test_the_map_is_not_loaded_until_the_visitor_asks_for_it(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertDontSee('<iframe', false)
            ->assertSee(route('facilities.show', ['facility' => $facility, 'map' => 1]), false)
            ->assertSee(__('facilities.show_map'))
            ->assertSee(__('facilities.map_privacy'));
    }

    public function test_the_map_appears_when_requested(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertOk()
            ->assertSee('<iframe', false)
            ->assertSee('https://www.openstreetmap.org/export/embed.html', false)
            ->assertSee('marker=-1.5%2C37.2', false)
            ->assertSee(__('facilities.map_credit'))
            ->assertSee(__('facilities.hide_map'));
    }

    public function test_the_map_is_centred_on_the_facility(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertSee('bbox=37.19%2C-1.51%2C37.21%2C-1.49', false);
    }

    public function test_the_embedded_map_is_accessible_and_limits_what_it_shares(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertSee('title="'.__('facilities.map_title').'"', false)
            ->assertSee('referrerpolicy="no-referrer"', false)
            ->assertSee('loading="lazy"', false)
            ->assertSee('sandbox="allow-scripts allow-same-origin"', false);
    }

    public function test_only_the_exact_value_1_turns_the_map_on(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 'yes']))
            ->assertOk()
            ->assertDontSee('<iframe', false);

        $this->get('/facilities/'.$facility->id.'?map[]=1')
            ->assertOk()
            ->assertDontSee('<iframe', false);
    }

    public function test_the_hide_link_returns_to_the_page_without_the_map(): void
    {
        $facility = $this->facilityWithLocation();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertSee('href="'.route('facilities.show', $facility).'"', false);
    }

    public function test_there_is_no_map_section_without_coordinates(): void
    {
        $facility = Facility::factory()->create(['latitude' => null, 'longitude' => null]);

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertDontSee(__('facilities.show_map'))
            ->assertDontSee('<iframe', false);

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertOk()
            ->assertDontSee('<iframe', false);
    }
}
