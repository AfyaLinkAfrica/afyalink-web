<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LayoutChromeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_header_has_a_decorative_brand_mark_and_a_labelled_navigation(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('class="brand-mark"', false)
            ->assertSee('aria-hidden="true"', false)
            ->assertSee('aria-label="'.__('layout.nav_label').'"', false)
            ->assertSee(__('layout.nav_home'))
            ->assertSee(__('layout.nav_facilities'));
    }

    public function test_the_emergency_notice_comes_before_the_header(): void
    {
        $html = $this->get(route('home'))->getContent();

        $this->assertLessThan(
            strpos($html, '<header class="site-header">'),
            strpos($html, 'class="emergency"'),
        );
    }

    public function test_the_skip_link_points_at_the_main_content(): void
    {
        $this->get(route('home'))
            ->assertSee('href="#main-content"', false)
            ->assertSee('<main id="main-content">', false)
            ->assertSee(__('layout.skip_to_content'));
    }

    public function test_the_ask_page_is_linked_from_the_header_and_the_footer(): void
    {
        if (! Route::has('assistant.show')) {
            $this->markTestSkipped('The assistant page is not installed yet.');
        }

        $html = $this->get(route('home'))->getContent();

        $this->assertSame(2, substr_count($html, 'href="'.route('assistant.show').'"'));
    }

    public function test_the_current_page_is_marked_in_the_header_only(): void
    {
        if (! Route::has('assistant.show')) {
            $this->markTestSkipped('The assistant page is not installed yet.');
        }

        $current = '/<a href="[^"]*\/assistant"\s+aria-current="page"/';

        $this->assertMatchesRegularExpression($current, $this->get(route('assistant.show'))->getContent());
        $this->assertDoesNotMatchRegularExpression($current, $this->get(route('facilities.index'))->getContent());
    }

    public function test_the_footer_has_three_groups_with_headings(): void
    {
        $this->get(route('home'))
            ->assertSee('class="footer-grid"', false)
            ->assertSee(__('layout.footer_about'))
            ->assertSee(__('layout.footer_explore'))
            ->assertSee(__('layout.footer_help'))
            ->assertSee(__('layout.footer_data_note'))
            ->assertSee(__('layout.footer_mistake'));
    }

    public function test_the_footer_keeps_the_disclaimer_and_the_open_source_note(): void
    {
        $this->get(route('home'))
            ->assertSee(__('layout.disclaimer'))
            ->assertSee(__('layout.footer_open_source'))
            ->assertSee('https://github.com/AfyaLinkAfrica/afyalink-web', false);
    }

    public function test_the_two_navigations_have_different_labels(): void
    {
        $this->assertNotSame(__('layout.nav_label'), __('layout.footer_nav_label'));

        $this->get(route('home'))
            ->assertSee('aria-label="'.__('layout.nav_label').'"', false)
            ->assertSee('aria-label="'.__('layout.footer_nav_label').'"', false);
    }

    public function test_the_header_and_footer_do_not_link_to_the_admin_area(): void
    {
        $this->get(route('home'))->assertDontSee('/admin', false);
        $this->get(route('facilities.index'))->assertDontSee('/admin', false);
    }
}
