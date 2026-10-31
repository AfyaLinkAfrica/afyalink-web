<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NearMeTest extends TestCase
{
    use RefreshDatabase;

    private function located(): Facility
    {
        return Facility::factory()->create(['latitude' => -1.2921, 'longitude' => 36.8219]);
    }

    private function unlocated(): Facility
    {
        return Facility::factory()->create(['latitude' => null, 'longitude' => null]);
    }

    public function test_the_listing_offers_distance_sorting_when_a_facility_has_a_location(): void
    {
        $this->located();

        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('id="near-me"', false)
            ->assertSee(__('nearme.sort'))
            ->assertSee(__('nearme.page_only'));
    }

    public function test_the_privacy_promise_is_shown_next_to_the_button(): void
    {
        $this->located();

        $this->get(route('facilities.index'))->assertSee(__('nearme.privacy'));
    }

    public function test_the_panel_is_hidden_until_the_browser_can_share_a_location(): void
    {
        $this->located();

        $this->get(route('facilities.index'))->assertSee('id="near-me" aria-label', false);
        $html = $this->get(route('facilities.index'))->getContent();

        $this->assertMatchesRegularExpression('/<section class="near-me" id="near-me"[^>]*\shidden>/', $html);
    }

    public function test_only_facilities_with_a_full_location_carry_coordinates(): void
    {
        $this->located();
        $this->unlocated();

        $html = $this->get(route('facilities.index'))->getContent();

        $this->assertSame(1, substr_count($html, 'data-lat="'));
        $this->assertSame(1, substr_count($html, 'data-lng="'));
    }

    public function test_there_is_no_distance_sorting_when_no_facility_has_a_location(): void
    {
        $this->unlocated();

        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertDontSee('id="near-me"', false)
            ->assertDontSee('geolocation', false);
    }

    public function test_there_is_no_distance_sorting_when_there_are_no_results(): void
    {
        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertDontSee('id="near-me"', false)
            ->assertDontSee('geolocation', false);
    }

    public function test_distance_sorting_is_offered_on_later_pages_too(): void
    {
        Facility::factory()->count(16)->create(['latitude' => -1.2921, 'longitude' => 36.8219]);

        $this->get(route('facilities.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('id="near-me"', false);
    }

    public function test_the_location_is_never_sent_or_stored(): void
    {
        $this->located();

        $html = $this->get(route('facilities.index'))->getContent();

        foreach (['fetch(', 'XMLHttpRequest', 'sendBeacon', 'localStorage', 'sessionStorage', 'document.cookie', 'indexedDB'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $html, "The page must not use {$forbidden}");
        }
    }

    public function test_the_location_is_requested_only_after_the_button_is_pressed(): void
    {
        $this->located();

        $html = $this->get(route('facilities.index'))->getContent();

        $this->assertSame(1, substr_count($html, 'getCurrentPosition'));
        $this->assertLessThan(
            strpos($html, 'getCurrentPosition'),
            strpos($html, "sortButton.addEventListener('click'"),
            'The location request must sit inside the click handler.',
        );
    }

    public function test_the_messages_for_refused_and_failed_lookups_are_available(): void
    {
        $this->located();

        $this->get(route('facilities.index'))
            ->assertSee(__('nearme.denied'))
            ->assertSee(__('nearme.failed'))
            ->assertSee(__('nearme.sorted'));
    }
}
