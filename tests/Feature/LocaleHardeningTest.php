<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use Tests\TestCase;

class LocaleHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['afyalink.locales' => ['en' => 'English', 'sw' => 'Kiswahili']]);
    }

    public function test_control_characters_in_the_return_path_send_the_visitor_home(): void
    {
        foreach (['/a%00b', '/a%0d%0aSet-Cookie:x=1', '/a%0ab', '/a%09b', '/a%7fb', '/a%20b'] as $bad) {
            $this->get('/language/sw?return='.$bad)
                ->assertRedirect('/')
                ->assertCookie(SetLocale::COOKIE, 'sw');
        }
    }

    public function test_an_ordinary_path_with_a_query_string_is_still_accepted(): void
    {
        $return = '/facilities?county=Kisumu&type=hospital&q=%C3%A9';

        $this->get(route('locale.switch', ['locale' => 'sw', 'return' => $return]))
            ->assertRedirect($return);
    }

    public function test_other_sites_are_still_refused(): void
    {
        foreach (['https://example.org/x', '//example.org/x', '/\\example.org/x'] as $bad) {
            $this->get(route('locale.switch', ['locale' => 'sw', 'return' => $bad]))->assertRedirect('/');
        }
    }

    public function test_pages_say_they_vary_by_the_language_cookie(): void
    {
        $this->assertStringContainsString('Cookie', (string) $this->get(route('home'))->headers->get('Vary'));
        $this->assertStringContainsString('Cookie', (string) $this->get(route('facilities.index'))->headers->get('Vary'));
        $this->assertStringContainsString('Cookie', (string) $this->get('/language/sw')->headers->get('Vary'));
    }
}
