<?php

namespace App\Http\Controllers;

use App\Support\Assistant\EmergencyDetector;
use App\Support\Assistant\RuleBasedExtractor;
use App\Support\Assistant\Understanding;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistantController extends Controller
{
    public const MAX_LENGTH = 300;

    public function show(): View
    {
        return $this->page();
    }

    /**
     * The answer is rendered straight from this POST. The message is never redirected,
     * flashed to the session, put in a URL, logged, or stored.
     */
    public function ask(Request $request, RuleBasedExtractor $extractor, EmergencyDetector $emergencyDetector): View
    {
        $message = $request->input('message');

        if (! is_string($message) || trim($message) === '') {
            return $this->page(asked: true, error: __('assistant.error_empty'));
        }

        $emergency = $emergencyDetector->matches($message);

        if (mb_strlen($message) > self::MAX_LENGTH) {
            return $this->page(asked: true, error: __('assistant.error_too_long'), emergency: $emergency);
        }

        $understanding = $extractor->extract($message);

        return $this->page(
            message: $message,
            asked: true,
            emergency: $emergency,
            understood: $this->understood($understanding),
            resultsUrl: $understanding->filters() === [] ? null : route('facilities.index', $understanding->filters()),
            choices: $this->choices($understanding),
            notUnderstood: $understanding->isEmpty(),
        );
    }

    /**
     * @param  list<array{label: string, value: string}>  $understood
     * @param  list<array{question: string, links: list<array{text: string, url: string}>}>  $choices
     */
    private function page(
        string $message = '',
        bool $asked = false,
        ?string $error = null,
        bool $emergency = false,
        array $understood = [],
        ?string $resultsUrl = null,
        array $choices = [],
        bool $notUnderstood = false,
    ): View {
        return view('assistant.index', compact(
            'message', 'asked', 'error', 'emergency', 'understood', 'resultsUrl', 'choices', 'notUnderstood',
        ));
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function understood(Understanding $understanding): array
    {
        $rows = [];

        if ($county = $understanding->county()) {
            $rows[] = ['label' => __('facilities.county'), 'value' => $county];
        }

        if ($type = $understanding->type()) {
            $rows[] = ['label' => __('facilities.type'), 'value' => __('facilities.types.'.$type)];
        }

        if ($service = $understanding->service()) {
            $rows[] = ['label' => __('facilities.service'), 'value' => $service['name']];
        }

        return $rows;
    }

    /**
     * When a message names more than one county, type, or service, ask which one is meant.
     *
     * @return list<array{question: string, links: list<array{text: string, url: string}>}>
     */
    private function choices(Understanding $understanding): array
    {
        $county = $understanding->county();
        $type = $understanding->type();
        $service = $understanding->service()['slug'] ?? null;
        $choices = [];

        if (count($understanding->counties) > 1) {
            $choices[] = [
                'question' => __('assistant.choose_county'),
                'links' => array_map(fn (string $option) => [
                    'text' => $option,
                    'url' => route('facilities.index', Understanding::query($option, $type, $service)),
                ], $understanding->counties),
            ];
        }

        if (count($understanding->types) > 1) {
            $choices[] = [
                'question' => __('assistant.choose_type'),
                'links' => array_map(fn (string $option) => [
                    'text' => __('facilities.types.'.$option),
                    'url' => route('facilities.index', Understanding::query($county, $option, $service)),
                ], $understanding->types),
            ];
        }

        if (count($understanding->services) > 1) {
            $choices[] = [
                'question' => __('assistant.choose_service'),
                'links' => array_map(fn (array $option) => [
                    'text' => $option['name'],
                    'url' => route('facilities.index', Understanding::query($county, $type, $option['slug'])),
                ], $understanding->services),
            ];
        }

        return $choices;
    }
}
