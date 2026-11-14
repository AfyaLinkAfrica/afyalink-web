<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Facility;
use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class LanguageSwitcherTest extends TestCase
{
    use RefreshDatabase;

    private function swahili(): static
    {
        return $this->withCookie(SetLocale::COOKIE, 'sw');
    }

    public function test_english_is_the_default_language(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee(__('home.search_heading', [], 'en'));
    }

    public function test_the_header_offers_both_languages_as_a_labelled_navigation(): void
    {
        $this->get(route('home'))
            ->assertSee('<nav class="lang-switch" aria-label="'.__('layout.language_label', [], 'en').'">', false)
            ->assertSee('lang="sw" hreflang="sw"', false)
            ->assertSee('Kiswahili')
            ->assertSee('<span class="current" lang="en" aria-current="true">English</span>', false);
    }

    public function test_the_switcher_works_without_javascript_because_it_is_a_plain_link(): void
    {
        $html = $this->get(route('facilities.index', ['county' => 'Nairobi']))->getContent();

        $this->assertStringContainsString(
            'href="'.e(route('locale.switch', ['locale' => 'sw', 'return' => '/facilities?county=Nairobi'])).'"',
            $html,
        );
    }

    public function test_choosing_a_language_sets_a_cookie_and_returns_to_the_same_page(): void
    {
        $response = $this->get(route('locale.switch', ['locale' => 'sw', 'return' => '/facilities?county=Nairobi']));

        $response->assertRedirect('/facilities?county=Nairobi');
        $response->assertCookie(SetLocale::COOKIE, 'sw');

        $cookie = $response->getCookie(SetLocale::COOKIE, false);
        $this->assertGreaterThan(now()->addDays(300)->timestamp, $cookie->getExpiresTime());
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertTrue($cookie->isHttpOnly());
    }

    public function test_the_cookie_is_marked_secure_on_https_and_not_on_plain_http(): void
    {
        $plain = $this->get(route('locale.switch', ['locale' => 'sw']))->getCookie(SetLocale::COOKIE, false);
        $this->assertFalse($plain->isSecure());

        $secure = $this->get('https://afyalink.test/language/sw')->getCookie(SetLocale::COOKIE, false);
        $this->assertTrue($secure->isSecure());
    }

    public function test_the_choice_persists_across_requests(): void
    {
        $response = $this->get(route('locale.switch', ['locale' => 'sw']))->assertRedirect('/');

        // Send back exactly what the browser would: the encrypted cookie value from the response.
        $stored = $response->getCookie(SetLocale::COOKIE, false)->getValue();
        $this->assertNotSame('sw', $stored);

        foreach ([route('home'), route('facilities.index'), route('assistant.show')] as $url) {
            $this->withUnencryptedCookie(SetLocale::COOKIE, $stored)
                ->get($url)
                ->assertOk()
                ->assertSee('<html lang="sw">', false);
        }

        $english = $this->get(route('locale.switch', ['locale' => 'en']))->getCookie(SetLocale::COOKIE, false)->getValue();

        $this->withUnencryptedCookie(SetLocale::COOKIE, $english)
            ->get(route('home'))
            ->assertSee('<html lang="en">', false);
    }

    public function test_an_unknown_language_is_rejected_and_sets_nothing(): void
    {
        foreach (['fr', 'SW', 'xx', '..', 'sw%00', 'en,sw'] as $code) {
            $this->get('/language/'.$code)->assertNotFound()->assertCookieMissing(SetLocale::COOKIE);
        }
    }

    public function test_an_unknown_language_in_the_cookie_is_ignored(): void
    {
        foreach (['fr', 'SW', '../../x', ''] as $code) {
            $this->withCookie(SetLocale::COOKIE, $code)
                ->get(route('home'))
                ->assertOk()
                ->assertSee('<html lang="en">', false);
        }
    }

    public function test_the_return_address_cannot_send_visitors_to_another_site(): void
    {
        $attempts = ['https://example.org/', '//example.org/', '/\\example.org', 'example.org', "/ok\r\nLocation: https://example.org", '', ['x']];

        foreach ($attempts as $return) {
            $this->get(route('locale.switch', ['locale' => 'sw', 'return' => $return]))->assertRedirect('/');
        }
    }

    public function test_the_listing_search_and_detail_pages_render_in_kiswahili(): void
    {
        $service = Service::factory()->create(['name' => 'Pharmacy', 'slug' => 'pharmacy']);
        $facility = Facility::factory()->verified()->create(['type' => 'health_centre']);
        $facility->services()->attach($service);

        $this->swahili()->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="sw">', false)
            ->assertSee(__('home.search_heading', [], 'sw'))
            ->assertSee(__('facilities.county', [], 'sw'))
            ->assertSee(__('services.pharmacy', [], 'sw'))
            ->assertSee('alt="'.__('home.logo_alt', ['name' => 'AfyaLink Africa'], 'sw').'"', false)
            ->assertDontSee('Find a health facility');

        $this->swahili()->get(route('facilities.index', ['q' => $facility->name]))
            ->assertOk()
            ->assertSee(__('facilities.search_button', [], 'sw'))
            ->assertSee(__('facilities.types.health_centre', [], 'sw'))
            ->assertSee(trans_choice('facilities.results_count', 1, ['count' => 1], 'sw'))
            ->assertSee(__('services.pharmacy', [], 'sw'))
            ->assertDontSee('Search facilities')
            ->assertDontSee('1 facility found');

        $this->swahili()->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertSee(__('facilities.last_verified', [], 'sw'))
            ->assertSee(__('facilities.back', [], 'sw'))
            ->assertSee($facility->verified_at->locale('sw')->translatedFormat('j F Y'))
            ->assertDontSee('Last verified');
    }

    public function test_the_report_form_and_assistant_render_in_kiswahili(): void
    {
        $facility = Facility::factory()->create();

        $this->swahili()->get(route('facilities.report.create', $facility))
            ->assertOk()
            ->assertSee(__('reports.message_label', [], 'sw'))
            ->assertSee(__('reports.submit', [], 'sw'));

        $this->swahili()->get(route('assistant.show'))
            ->assertOk()
            ->assertSee(__('assistant.title', [], 'sw'));
    }

    public function test_validation_messages_follow_the_chosen_language(): void
    {
        $facility = Facility::factory()->create();

        $this->swahili()->from(route('facilities.report.create', $facility))
            ->post(route('facilities.report.store', $facility), ['message' => 'short'])
            ->assertSessionHasErrors(['message' => __('reports.errors.message_min', ['min' => 10], 'sw')]);
    }

    public function test_the_page_chrome_is_translated(): void
    {
        $this->swahili()->get(route('home'))
            ->assertSee(__('layout.skip_to_content', [], 'sw'))
            ->assertSee(__('layout.emergency', [], 'sw'))
            ->assertSee(__('layout.disclaimer', [], 'sw'))
            ->assertSee(__('layout.footer_data_note', [], 'sw'));
    }

    public function test_the_switcher_marks_the_current_language_and_links_to_the_other(): void
    {
        $this->swahili()->get(route('home'))
            ->assertSee('<span class="current" lang="sw" aria-current="true">Kiswahili</span>', false)
            ->assertSee('lang="en" hreflang="en"', false);
    }

    public function test_every_language_file_has_the_same_keys_in_english_and_kiswahili(): void
    {
        $files = collect(glob(lang_path('en/*.php')))->map(fn (string $path) => basename($path))->sort()->values();

        $this->assertNotEmpty($files);
        $this->assertSame(
            $files->all(),
            collect(glob(lang_path('sw/*.php')))->map(fn (string $path) => basename($path))->sort()->values()->all(),
            'lang/sw must have exactly the same files as lang/en.',
        );

        foreach ($files as $file) {
            $english = array_keys(Arr::dot(require lang_path('en/'.$file)));
            $swahili = array_keys(Arr::dot(require lang_path('sw/'.$file)));

            $this->assertSame([], array_values(array_diff($english, $swahili)), "{$file}: keys missing from sw.");
            $this->assertSame([], array_values(array_diff($swahili, $english)), "{$file}: keys in sw but not in en.");
        }
    }

    public function test_no_kiswahili_string_is_empty_or_left_in_english(): void
    {
        // Names, the English-only assistant placeholder and its English examples may match.
        $allowed = ['site_name', 'title', 'name_placeholder', 'placeholder'];

        foreach (glob(lang_path('sw/*.php')) as $path) {
            $english = Arr::dot(require lang_path('en/'.basename($path)));

            foreach (Arr::dot(require $path) as $key => $value) {
                $this->assertNotSame('', trim((string) $value), basename($path).": {$key} is empty.");

                if (basename($path) === 'assistant.php' && str_starts_with($key, 'examples.')) {
                    continue;
                }

                if (basename($path) === 'home.php' && $key === 'title') {
                    continue;
                }

                if (! in_array($key, $allowed, true)) {
                    $this->assertNotSame($english[$key], $value, basename($path).": {$key} is not translated.");
                }
            }
        }
    }

    public function test_placeholders_in_each_kiswahili_string_match_the_english_ones(): void
    {
        foreach (glob(lang_path('en/*.php')) as $path) {
            $swahili = Arr::dot(require lang_path('sw/'.basename($path)));

            foreach (Arr::dot(require $path) as $key => $value) {
                preg_match_all('/:\w+/', $value, $english);
                preg_match_all('/:\w+/', $swahili[$key], $translated);

                $this->assertEqualsCanonicalizing($english[0], $translated[0], basename($path).": {$key} placeholders differ.");
            }
        }
    }

    public function test_every_standard_service_has_a_name_in_both_languages(): void
    {
        foreach (ServiceSeeder::NAMES as $name) {
            $slug = Str::slug($name);

            $this->assertSame($name, __('services.'.$slug, [], 'en'), "services.{$slug} must equal the seeded name.");
            $this->assertNotSame($name, __('services.'.$slug, [], 'sw'));
        }
    }

    public function test_a_service_without_a_translation_keeps_its_stored_name(): void
    {
        $service = Service::factory()->create(['name' => 'Wound dressing', 'slug' => 'wound-dressing']);

        app()->setLocale('sw');

        $this->assertSame('Wound dressing', $service->label);
    }

    public function test_health_related_kiswahili_wording_is_marked_for_review(): void
    {
        foreach (['layout', 'home', 'facilities', 'services', 'assistant', 'reports'] as $file) {
            $this->assertStringContainsString('healthcare-review', file_get_contents(lang_path("sw/{$file}.php")), "sw/{$file}.php has no review marker.");
        }

        // Every service name is health terminology, so each line must carry the marker.
        foreach (file(lang_path('sw/services.php')) as $line) {
            if (str_contains($line, '=>')) {
                $this->assertStringContainsString('healthcare-review', $line);
            }
        }
    }
}
