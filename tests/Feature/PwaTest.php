<?php

namespace Tests\Feature;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    private function worker(): string
    {
        return file_get_contents(public_path('sw.js'));
    }

    public function test_the_manifest_is_valid_and_complete(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertIsArray($manifest);
        $this->assertSame('AfyaLink Africa', $manifest['name']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertNotEmpty($manifest['theme_color']);
        $this->assertNotEmpty($manifest['background_color']);
    }

    public function test_every_manifest_icon_exists_with_the_declared_size(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);
        $declared = [];

        foreach ($manifest['icons'] as $icon) {
            $path = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($path);

            [$width, $height] = getimagesize($path);
            $this->assertSame($icon['sizes'], $width.'x'.$height);

            $declared[] = $icon['sizes'];
        }

        $this->assertContains('192x192', $declared);
        $this->assertContains('512x512', $declared);
    }

    public function test_pages_link_the_manifest_and_register_the_worker(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee("navigator.serviceWorker.register('/sw.js'", false);

        $facility = Facility::factory()->create();

        $this->get(route('facilities.show', $facility))
            ->assertOk()
            ->assertSee('rel="manifest"', false);
    }

    public function test_the_worker_is_registered_only_when_the_browser_supports_it(): void
    {
        $this->get(route('home'))->assertSee("'serviceWorker' in navigator", false);
    }

    public function test_the_offline_page_exists_and_keeps_the_emergency_notice(): void
    {
        $html = file_get_contents(public_path('offline.html'));

        $this->assertStringContainsString('You are offline', $html);
        $this->assertStringContainsString('In an emergency', $html);
        $this->assertStringContainsString('out of date', $html);
        $this->assertStringContainsString('lang="en"', $html);
    }

    public function test_the_worker_precaches_the_offline_page(): void
    {
        $this->assertStringContainsString("const OFFLINE_URL = '/offline.html';", $this->worker());
    }

    public function test_the_worker_only_handles_get_requests_to_this_site(): void
    {
        $worker = $this->worker();

        $this->assertStringContainsString("request.method !== 'GET'", $worker);
        $this->assertStringContainsString('url.origin !== self.location.origin', $worker);
    }

    public function test_the_worker_never_stores_maintainer_pages_or_the_report_form(): void
    {
        $this->assertStringContainsString("const NEVER_STORE_PATHS = ['/admin', '/report'];", $this->worker());
    }

    public function test_the_worker_never_stores_the_report_sent_page(): void
    {
        $facility = Facility::factory()->create();

        // The marker the worker looks for must be exactly what the thank-you notice renders.
        $this->withSession(['report_submitted' => true])
            ->get(route('facilities.show', $facility))
            ->assertSee('class="notice success"', false);

        $this->assertStringContainsString("const NO_STORE_MARKER = 'class=\"notice success\"';", $this->worker());
    }

    public function test_a_page_shown_from_a_saved_copy_says_so(): void
    {
        $worker = $this->worker();

        $this->assertStringContainsString('You are viewing a copy saved on', $worker);
        $this->assertStringContainsString('It may be out of date', $worker);
    }

    public function test_the_worker_file_itself_is_never_cached(): void
    {
        // The worker file must always be fetched fresh so updates reach visitors.
        $this->assertStringContainsString("pathname === '/sw.js'", $this->worker());
    }
}
