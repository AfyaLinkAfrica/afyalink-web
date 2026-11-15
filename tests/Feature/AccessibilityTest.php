<?php

namespace Tests\Feature;

use App\Models\Facility;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every page that has a form, with a facility created for the pages that need one.
     *
     * @return array<string, string>
     */
    private function pagesWithForms(): array
    {
        $facility = Facility::factory()->create();

        return [
            'home' => route('home'),
            'listing' => route('facilities.index'),
            'report form' => route('facilities.report.create', $facility),
            'assistant' => route('assistant.show'),
            'admin sign in' => route('login'),
        ];
    }

    public function test_every_form_field_has_a_label(): void
    {
        foreach ($this->pagesWithForms() as $name => $url) {
            $document = new DOMDocument;
            @$document->loadHTML($this->get($url)->assertOk()->getContent());
            $xpath = new DOMXPath($document);

            $fields = $xpath->query('//input[not(@type="hidden" or @type="submit" or @type="button")] | //select | //textarea');
            $this->assertGreaterThan(0, $fields->length, "No form fields found on the {$name} page.");

            foreach ($fields as $field) {
                $id = $field->getAttribute('id');
                $this->assertNotSame('', $id, "A field on the {$name} page has no id to label.");
                $this->assertGreaterThan(
                    0,
                    $xpath->query('//label[@for="'.$id.'"]')->length,
                    "The field #{$id} on the {$name} page has no label.",
                );
            }
        }
    }

    public function test_the_emergency_notice_is_a_labelled_landmark(): void
    {
        $this->get(route('home'))
            ->assertSee('<aside class="emergency" aria-label="'.__('layout.emergency_label').'">', false);
    }

    public function test_the_map_section_name_is_not_a_bare_map_label(): void
    {
        // The embedded OpenStreetMap page names its own region "Map"; ours must not collide with it.
        $facility = Facility::factory()->create();

        $this->get(route('facilities.show', ['facility' => $facility, 'map' => 1]))
            ->assertSee('<h2 id="map-heading">'.__('facilities.map').'</h2>', false);
        $this->assertNotSame('Map', __('facilities.map'));
    }

    /**
     * Read the colour tokens from the shared layout: the light set from :root, and the dark set
     * from the prefers-color-scheme block (dark values override the light ones).
     *
     * @return array{light: array<string, string>, dark: array<string, string>}
     */
    private function palettes(): array
    {
        $css = file_get_contents(resource_path('views/layouts/app.blade.php'));

        preg_match('/:root\s*\{(.*?)\}/s', $css, $root);
        preg_match('/prefers-color-scheme:\s*dark\)\s*\{\s*:root\s*\{(.*?)\}/s', $css, $dark);

        $parse = function (string $block): array {
            preg_match_all('/--([\w-]+):\s*(#[0-9a-fA-F]{6});/', $block, $matches, PREG_SET_ORDER);

            return array_column($matches, 2, 1);
        };

        $light = $parse($root[1]);

        return ['light' => $light, 'dark' => array_merge($light, $parse($dark[1]))];
    }

    private function luminance(string $hex): float
    {
        $channels = array_map(function (string $part): float {
            $value = hexdec($part) / 255;

            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, str_split(ltrim($hex, '#'), 2));

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function contrast(string $first, string $second): float
    {
        [$lighter, $darker] = [max($this->luminance($first), $this->luminance($second)), min($this->luminance($first), $this->luminance($second))];

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    public function test_text_colours_meet_aa_contrast_in_light_and_dark_mode(): void
    {
        $pairs = [
            ['text', 'bg'], ['text', 'card'], ['muted', 'bg'], ['muted', 'card'],
            ['brand', 'bg'], ['brand', 'card'], ['brand-dark', 'card'],
            ['on-brand', 'brand'], ['alert-text', 'alert-bg'],
        ];

        foreach ($this->palettes() as $scheme => $palette) {
            foreach ($pairs as [$foreground, $background]) {
                $this->assertGreaterThanOrEqual(
                    4.5,
                    $this->contrast($palette[$foreground], $palette[$background]),
                    "--{$foreground} on --{$background} is below 4.5:1 in {$scheme} mode.",
                );
            }
        }
    }

    public function test_form_control_borders_meet_non_text_contrast_in_light_and_dark_mode(): void
    {
        foreach ($this->palettes() as $scheme => $palette) {
            $this->assertArrayHasKey('control-border', $palette);

            foreach (['card', 'bg'] as $background) {
                $this->assertGreaterThanOrEqual(
                    3.0,
                    $this->contrast($palette['control-border'], $palette[$background]),
                    "--control-border on --{$background} is below 3:1 in {$scheme} mode.",
                );
            }
        }
    }

    public function test_placeholder_text_uses_a_token_with_enough_contrast(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertMatchesRegularExpression('/::placeholder\s*\{\s*color:\s*var\(--muted\);\s*opacity:\s*1;/', $layout);
    }
}
