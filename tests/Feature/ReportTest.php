<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'message' => 'The phone number on this page has changed.',
            'contact' => '+254700111222',
        ], $overrides);
    }

    public function test_report_form_shows_the_facility_and_the_privacy_warning(): void
    {
        $facility = Facility::factory()->create(['name' => 'Report Test Clinic']);

        $this->get(route('facilities.report.create', $facility))
            ->assertOk()
            ->assertSee('Report Test Clinic')
            ->assertSee('name="message"', false)
            ->assertSee('name="contact"', false)
            ->assertSee('name="hp_check"', false)
            ->assertSee(__('reports.privacy'));
    }

    public function test_report_form_for_an_unknown_facility_returns_404(): void
    {
        $this->get('/facilities/999999/report')->assertNotFound();
        $this->post('/facilities/999999/report', $this->validData())->assertNotFound();
    }

    public function test_a_valid_report_is_stored_and_the_visitor_is_thanked(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData())
            ->assertRedirect(route('facilities.show', $facility))
            ->assertSessionHas('report_submitted');

        $this->assertDatabaseHas('reports', [
            'facility_id' => $facility->id,
            'message' => 'The phone number on this page has changed.',
            'contact' => '+254700111222',
            'status' => Report::STATUS_NEW,
        ]);

        $this->withSession(['report_submitted' => true])
            ->get(route('facilities.show', $facility))
            ->assertSee(__('reports.thanks'));
    }

    public function test_the_contact_detail_is_optional(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData(['contact' => '']))
            ->assertRedirect(route('facilities.show', $facility));

        $this->assertDatabaseHas('reports', ['facility_id' => $facility->id, 'contact' => null]);
    }

    public function test_the_message_is_required(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData(['message' => '']))
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_the_message_must_be_long_enough_and_not_too_long(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData(['message' => 'too short']))
            ->assertSessionHasErrors('message');

        $this->post(route('facilities.report.store', $facility), $this->validData(['message' => str_repeat('a', 1001)]))
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_the_contact_detail_has_a_length_limit(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData(['contact' => str_repeat('1', 101)]))
            ->assertSessionHasErrors('contact');
    }

    public function test_the_form_keeps_what_the_visitor_typed_after_an_error(): void
    {
        $facility = Facility::factory()->create();
        $form = route('facilities.report.create', $facility);

        $this->from($form)
            ->post(route('facilities.report.store', $facility), $this->validData(['message' => 'short', 'contact' => 'me@example.com']))
            ->assertRedirect($form);

        $this->get($form)
            ->assertSee('short')
            ->assertSee('me@example.com');
    }

    public function test_a_filled_honeypot_looks_like_success_but_stores_nothing(): void
    {
        $facility = Facility::factory()->create();

        $this->post(route('facilities.report.store', $facility), $this->validData(['hp_check' => 'spam-bot']))
            ->assertRedirect(route('facilities.show', $facility))
            ->assertSessionHas('report_submitted');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        $facility = Facility::factory()->create();
        $url = route('facilities.report.store', $facility);

        foreach (range(1, 5) as $attempt) {
            $this->post($url, $this->validData())->assertRedirect();
        }

        $this->post($url, $this->validData())->assertStatus(429);

        $this->assertDatabaseCount('reports', 5);
    }

    public function test_the_detail_page_links_to_the_report_form(): void
    {
        $facility = Facility::factory()->create();

        $this->get(route('facilities.show', $facility))
            ->assertSee(route('facilities.report.create', $facility), false)
            ->assertSee(__('reports.link'));
    }

    public function test_reports_belong_to_a_facility_and_are_removed_with_it(): void
    {
        $facility = Facility::factory()->create();
        Report::factory()->create(['facility_id' => $facility->id]);

        $this->assertSame(1, $facility->reports()->count());

        $facility->delete();

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_reports_do_not_store_visitor_addresses(): void
    {
        foreach (['ip', 'ip_address', 'user_agent', 'user_id'] as $column) {
            $this->assertFalse(Schema::hasColumn('reports', $column), "reports must not have a {$column} column.");
        }
    }
}
