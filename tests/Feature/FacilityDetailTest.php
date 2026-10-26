<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_page_shows_the_facility_details(): void
    {
        $facility = Facility::factory()->create([
            'name' => 'Detail Test Clinic',
            'county' => 'Machakos',
            'sub_county' => 'Mavoko',
            'address' => 'Along Main Road',
            'phone' => '+254700123456',
            'type' => 'clinic',
        ]);

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertSee('Detail Test Clinic')
            ->assertSee('Machakos')
            ->assertSee('Mavoko')
            ->assertSee('Along Main Road')
            ->assertSee('tel:+254700123456', false)
            ->assertSee(__('facilities.types.clinic'));
    }

    public function test_detail_page_uses_the_shared_layout_and_page_title(): void
    {
        $facility = Facility::factory()->create(['name' => 'Detail Test Clinic']);

        $this->get(route('facilities.show', $facility))
            ->assertSee('<main id="main-content">', false)
            ->assertSee('<title>Detail Test Clinic | AfyaLink Africa</title>', false)
            ->assertSee(__('layout.disclaimer'));
    }

    public function test_map_link_appears_only_when_coordinates_exist(): void
    {
        $withMap = Facility::factory()->create(['latitude' => -1.286, 'longitude' => 36.817]);
        $withoutMap = Facility::factory()->create(['latitude' => null, 'longitude' => null]);

        $this->get(route('facilities.show', $withMap))
            ->assertSee('openstreetmap.org', false)
            ->assertSee('mlat=-1.2860000', false);

        $this->get(route('facilities.show', $withoutMap))
            ->assertDontSee('openstreetmap.org', false);
    }

    public function test_optional_fields_are_hidden_when_missing(): void
    {
        $facility = Facility::factory()->create([
            'sub_county' => null,
            'address' => null,
            'phone' => null,
        ]);

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertDontSee('tel:', false)
            ->assertDontSee(__('facilities.sub_county'))
            ->assertDontSee(__('facilities.address'));
    }

    public function test_unverified_facilities_show_a_warning(): void
    {
        $facility = Facility::factory()->unverified()->create();

        $this->get(route('facilities.show', $facility))
            ->assertSee(__('facilities.unverified_note'))
            ->assertSee(__('facilities.not_verified'));
    }

    public function test_verified_facilities_show_the_verification_date_without_a_warning(): void
    {
        $facility = Facility::factory()->verified()->create();

        $this->get(route('facilities.show', $facility))
            ->assertSee($facility->verified_at->translatedFormat('j F Y'))
            ->assertDontSee(__('facilities.unverified_note'));
    }

    public function test_demo_data_is_labelled_but_real_data_is_not(): void
    {
        $demo = Facility::factory()->create(['source' => 'synthetic']);
        $real = Facility::factory()->create(['source' => 'approved-source']);

        $this->get(route('facilities.show', $demo))
            ->assertSee(__('facilities.demo_notice'));

        $this->get(route('facilities.show', $real))
            ->assertDontSee(__('facilities.demo_notice'));
    }

    public function test_unknown_facility_returns_404(): void
    {
        $this->get('/facilities/999999')->assertNotFound();
        $this->get('/facilities/not-a-number')->assertNotFound();
    }

    public function test_listing_links_to_the_detail_page(): void
    {
        $facility = Facility::factory()->create();

        $this->get(route('facilities.index'))
            ->assertSee(route('facilities.show', $facility), false);
    }

    public function test_back_link_returns_to_the_listing(): void
    {
        $facility = Facility::factory()->create();

        $this->get(route('facilities.show', $facility))
            ->assertSee('href="'.route('facilities.index').'"', false)
            ->assertSee(__('facilities.back'));
    }
}
