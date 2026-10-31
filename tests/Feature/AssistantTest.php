<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ServiceSeeder::class);
    }

    private function slug(string $name): string
    {
        return Service::where('name', $name)->value('slug');
    }

    private function ask(string $message)
    {
        return $this->post(route('assistant.ask'), ['message' => $message]);
    }

    private function link(array $filters): string
    {
        return e(route('facilities.index', $filters));
    }

    public function test_the_page_loads_with_the_form_and_the_limits(): void
    {
        $this->get(route('assistant.show'))
            ->assertOk()
            ->assertSee(__('assistant.label'))
            ->assertSee(__('assistant.limits'))
            ->assertSee('Child immunisation in Kisumu');
    }

    public function test_a_service_and_a_county_become_a_search(): void
    {
        $this->ask('child immunisation in Kisumu')
            ->assertOk()
            ->assertSee(__('assistant.understood_title'))
            ->assertSee($this->link(['county' => 'Kisumu', 'service' => $this->slug('Child immunisation')]), false);
    }

    public function test_everyday_words_are_understood(): void
    {
        $this->ask('where can I get a vaccine for my baby near Mombasa?')
            ->assertSee($this->link(['county' => 'Mombasa', 'service' => $this->slug('Child immunisation')]), false);

        $this->ask('Looking for HIV testing, Kakamega')
            ->assertSee($this->link(['county' => 'Kakamega', 'service' => $this->slug('HIV testing and counselling')]), false);
    }

    public function test_a_facility_type_is_understood(): void
    {
        $this->ask('a hospital in Kisumu with a pharmacy')
            ->assertSee($this->link(['county' => 'Kisumu', 'type' => 'hospital', 'service' => $this->slug('Pharmacy')]), false);

        $this->ask('health centre in Mombasa')
            ->assertSee($this->link(['county' => 'Mombasa', 'type' => 'health_centre']), false);
    }

    public function test_the_word_clinic_is_a_type_only_when_no_service_is_named(): void
    {
        $this->ask('clinic in Kisumu')
            ->assertSee($this->link(['county' => 'Kisumu', 'type' => 'clinic']), false);

        $this->ask('antenatal clinic in Kisumu')
            ->assertSee($this->link(['county' => 'Kisumu', 'service' => $this->slug('Antenatal and postnatal care')]), false)
            ->assertDontSee('type=clinic', false);
    }

    public function test_hyphenated_and_apostrophe_county_names_match(): void
    {
        $this->ask('pharmacy in taita taveta')
            ->assertSee($this->link(['county' => 'Taita-Taveta', 'service' => $this->slug('Pharmacy')]), false);
    }

    public function test_more_than_one_county_asks_which_one(): void
    {
        $this->ask('pharmacy in Kisumu or Kakamega')
            ->assertSee(__('assistant.choose_county'))
            ->assertSee($this->link(['county' => 'Kisumu', 'service' => $this->slug('Pharmacy')]), false)
            ->assertSee($this->link(['county' => 'Kakamega', 'service' => $this->slug('Pharmacy')]), false);
    }

    public function test_more_than_one_service_asks_which_one(): void
    {
        $this->ask('dental or eye care in Mombasa')
            ->assertSee(__('assistant.choose_service'))
            ->assertSee($this->link(['county' => 'Mombasa', 'service' => $this->slug('Dental care')]), false)
            ->assertSee($this->link(['county' => 'Mombasa', 'service' => $this->slug('Eye care')]), false);
    }

    public function test_a_message_with_nothing_to_search_for_says_so_and_offers_the_full_search(): void
    {
        $this->ask('what is the weather like today')
            ->assertOk()
            ->assertSee(__('assistant.not_understood'))
            ->assertSee(route('facilities.index'), false);
    }

    public function test_possible_emergencies_get_a_notice_first(): void
    {
        $this->ask("my child can't breathe, hospital in Kisumu")
            ->assertSee(__('assistant.emergency'))
            ->assertSee($this->link(['county' => 'Kisumu', 'type' => 'hospital']), false);

        $this->ask('there is heavy bleeding')->assertSee(__('assistant.emergency'));
    }

    public function test_ordinary_requests_do_not_show_the_emergency_notice(): void
    {
        $this->ask('child immunisation in Kisumu')->assertDontSee(__('assistant.emergency'));
        $this->get(route('assistant.show'))->assertDontSee(__('assistant.emergency'));
    }

    public function test_an_empty_or_non_text_message_is_refused_without_errors(): void
    {
        $this->post(route('assistant.ask'), ['message' => ''])->assertOk()->assertSee(__('assistant.error_empty'));
        $this->post(route('assistant.ask'), ['message' => '   '])->assertOk()->assertSee(__('assistant.error_empty'));
        $this->post(route('assistant.ask'), ['message' => ['x']])->assertOk()->assertSee(__('assistant.error_empty'));
        $this->post(route('assistant.ask'))->assertOk()->assertSee(__('assistant.error_empty'));
    }

    public function test_a_message_over_300_characters_is_refused(): void
    {
        $this->ask(str_repeat('a', 301))->assertOk()->assertSee(__('assistant.error_too_long'));
        $this->ask(str_repeat('a', 300))->assertDontSee(__('assistant.error_too_long'));
    }

    public function test_the_message_is_shown_back_escaped(): void
    {
        $this->ask('<script>alert(1)</script> pharmacy')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_the_message_is_not_stored_in_the_session_or_a_url(): void
    {
        $response = $this->ask('pharmacy in Kisumu')->assertOk();

        $response->assertSessionMissing('_old_input');
        $this->assertStringNotContainsString('pharmacy in Kisumu', json_encode(session()->all()));
        $this->assertNull($response->headers->get('Location'));
    }

    public function test_the_assistant_is_rate_limited(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->ask('pharmacy in Kisumu')->assertOk();
        }

        $this->ask('pharmacy in Kisumu')->assertStatus(429);
    }

    public function test_the_assistant_never_calls_an_outside_service(): void
    {
        $classes = [
            'app/Http/Controllers/AssistantController.php',
            'app/Support/Assistant/RuleBasedExtractor.php',
            'app/Support/Assistant/EmergencyDetector.php',
        ];

        foreach ($classes as $file) {
            $code = file_get_contents(base_path($file));

            foreach (['Http::', 'curl_', 'file_get_contents(\'http', 'Guzzle', 'Log::'] as $forbidden) {
                $this->assertStringNotContainsString($forbidden, $code, "{$file} must not use {$forbidden}");
            }
        }
    }
}
