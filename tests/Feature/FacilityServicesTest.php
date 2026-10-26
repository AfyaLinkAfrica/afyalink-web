<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Service;
use Database\Seeders\FacilitySeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityServicesTest extends TestCase
{
    use RefreshDatabase;

    private function service(string $name, string $slug): Service
    {
        return Service::factory()->create(['name' => $name, 'slug' => $slug]);
    }

    public function test_a_facility_can_offer_many_services_and_a_service_many_facilities(): void
    {
        $facility = Facility::factory()->create();
        $service = $this->service('Pharmacy', 'pharmacy');

        $facility->services()->attach($service);

        $this->assertTrue($facility->services->contains($service));
        $this->assertTrue($service->facilities->contains($facility));
    }

    public function test_offering_service_scope_filters_and_ignores_null(): void
    {
        $service = $this->service('Pharmacy', 'pharmacy');
        $with = Facility::factory()->create();
        Facility::factory()->create();
        $with->services()->attach($service);

        $this->assertSame(1, Facility::offeringService('pharmacy')->count());
        $this->assertSame(2, Facility::offeringService(null)->count());
    }

    public function test_service_filter_limits_the_listing(): void
    {
        $service = $this->service('Child immunisation', 'child-immunisation');
        Facility::factory()->create(['name' => 'Offers Immunisation'])->services()->attach($service);
        Facility::factory()->create(['name' => 'Does Not Offer It']);

        $this->get(route('facilities.index', ['service' => 'child-immunisation']))
            ->assertOk()
            ->assertSee('Offers Immunisation')
            ->assertDontSee('Does Not Offer It');
    }

    public function test_service_filter_combines_with_county(): void
    {
        $service = $this->service('Pharmacy', 'pharmacy');
        Facility::factory()->create(['name' => 'Match Me', 'county' => 'Kisumu'])->services()->attach($service);
        Facility::factory()->create(['name' => 'Wrong County', 'county' => 'Nairobi'])->services()->attach($service);

        $this->get(route('facilities.index', ['service' => 'pharmacy', 'county' => 'Kisumu']))
            ->assertSee('Match Me')
            ->assertDontSee('Wrong County');
    }

    public function test_unknown_or_non_text_service_values_are_ignored(): void
    {
        $this->service('Pharmacy', 'pharmacy');
        Facility::factory()->count(3)->create();

        $unknown = $this->get(route('facilities.index', ['service' => 'time-travel']))->assertOk();
        $this->assertSame(3, $unknown->viewData('facilities')->total());

        $this->get('/facilities?service[]=pharmacy')->assertOk();
    }

    public function test_listing_cards_show_service_names(): void
    {
        $facility = Facility::factory()->create();
        $facility->services()->attach($this->service('Dental care', 'dental-care'));

        $this->get(route('facilities.index'))->assertSee('Dental care');
    }

    public function test_detail_page_lists_services(): void
    {
        $facility = Facility::factory()->create();
        $facility->services()->attach([
            $this->service('Dental care', 'dental-care')->id,
            $this->service('Eye care', 'eye-care')->id,
        ]);

        $this->get(route('facilities.show', $facility))
            ->assertSee('Dental care')
            ->assertSee('Eye care')
            ->assertSee(__('facilities.services'));
    }

    public function test_detail_page_has_no_services_row_when_none_are_listed(): void
    {
        $facility = Facility::factory()->create();

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertDontSee('class="services"', false);
    }

    public function test_search_form_offers_services_and_keeps_the_selection(): void
    {
        $this->service('Pharmacy', 'pharmacy');

        $this->get(route('facilities.index', ['service' => 'pharmacy']))
            ->assertSee('name="service"', false)
            ->assertSee('<option value="pharmacy" selected>', false);
    }

    public function test_service_filter_is_hidden_when_no_services_exist(): void
    {
        $this->get(route('facilities.index'))
            ->assertOk()
            ->assertDontSee('name="service"', false);
    }

    public function test_home_page_links_to_each_service(): void
    {
        $this->service('Pharmacy', 'pharmacy');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('home.browse_services_heading'))
            ->assertSee(route('facilities.index', ['service' => 'pharmacy']), false);
    }

    public function test_home_page_has_no_service_section_without_services(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee(__('home.browse_services_heading'));
    }

    public function test_pagination_keeps_the_service_filter(): void
    {
        $service = $this->service('Pharmacy', 'pharmacy');
        Facility::factory()->count(20)->create()->each(fn (Facility $facility) => $facility->services()->attach($service));

        $this->get(route('facilities.index', ['service' => 'pharmacy']))
            ->assertSee('service=pharmacy', false)
            ->assertSee('page=2', false);
    }

    public function test_service_seeder_can_run_repeatedly_without_duplicates(): void
    {
        $this->seed(ServiceSeeder::class);
        $this->seed(ServiceSeeder::class);

        $this->assertSame(count(ServiceSeeder::NAMES), Service::count());
    }

    public function test_facility_seeder_gives_every_facility_at_least_one_service(): void
    {
        $this->seed(FacilitySeeder::class);

        $this->assertSame(50, Facility::count());
        $this->assertSame(0, Facility::doesntHave('services')->count());
    }
}
