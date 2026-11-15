<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiswahili is off until its health wording is approved, so the default configuration must offer
 * English only. This test deliberately does not enable it.
 */
class SwahiliGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_by_default_only_english_is_offered(): void
    {
        $this->assertSame(['en' => 'English'], config('afyalink.locales'));
    }

    public function test_by_default_the_switcher_is_not_shown(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('lang-switch', false)
            ->assertDontSee('/language/', false);
    }

    public function test_by_default_choosing_kiswahili_is_a_404_and_sets_nothing(): void
    {
        $this->get('/language/sw')->assertNotFound()->assertCookieMissing(SetLocale::COOKIE);
    }

    public function test_by_default_a_kiswahili_cookie_is_ignored(): void
    {
        $this->withCookie(SetLocale::COOKIE, 'sw')->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="en">', false);
    }
}
