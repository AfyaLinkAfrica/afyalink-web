@extends('layouts.app')

@section('title', __('assistant.title'))

@push('styles')
<style>
    ul.understood, ul.examples { margin: .5rem 0 0; padding-left: 1.25rem; color: var(--muted); }
    ul.understood strong { color: var(--text); }
    ul.choices { display: flex; flex-wrap: wrap; gap: .5rem; margin: .5rem 0 1rem; padding: 0; list-style: none; }
    .assistant-result { margin-top: 1.5rem; }
    .assistant-result h2 { margin: 0 0 .25rem; font-size: 1.2rem; }
</style>
@endpush

@section('content')
    @if ($emergency)
        <p class="notice" role="alert" id="emergency">{{ __('assistant.emergency') }}</p>
    @endif

    <section class="card">
        <h1>{{ __('assistant.title') }}</h1>
        <p>{{ __('assistant.intro') }}</p>
        <p class="help">{{ __('assistant.limits') }}</p>

        <form method="POST" action="{{ route('assistant.ask') }}#result">
            @csrf
            <div class="field">
                <label for="message">{{ __('assistant.label') }}</label>
                <input id="message" name="message" type="text" maxlength="300" value="{{ $message }}" placeholder="{{ __('assistant.placeholder') }}" autocomplete="off" required>
                @if ($error)
                    <p class="error" role="alert">{{ $error }}</p>
                @endif
            </div>
            <button type="submit" class="button">{{ __('assistant.button') }}</button>
        </form>

        @if (! $asked || $notUnderstood || $error)
            <h2>{{ __('assistant.examples_title') }}</h2>
            <ul class="examples">
                @foreach (__('assistant.examples') as $example)
                    <li>{{ $example }}</li>
                @endforeach
            </ul>
        @endif
    </section>

    @if ($asked && $error === null)
        <section class="card assistant-result" id="result" aria-live="polite">
            @if ($notUnderstood)
                <p>{{ __('assistant.not_understood') }}</p>
                <p><a href="{{ route('facilities.index') }}">{{ __('assistant.full_search') }}</a></p>
            @else
                @if ($understood !== [])
                    <h2>{{ __('assistant.understood_title') }}</h2>
                    <ul class="understood">
                        @foreach ($understood as $row)
                            <li>{{ $row['label'] }}: <strong>{{ $row['value'] }}</strong></li>
                        @endforeach
                    </ul>
                @endif

                @if ($resultsUrl)
                    <div class="actions">
                        <a class="button primary" href="{{ $resultsUrl }}">{{ __('assistant.show') }}</a>
                    </div>
                @endif

                @foreach ($choices as $choice)
                    <p>{{ $choice['question'] }}</p>
                    <ul class="choices">
                        @foreach ($choice['links'] as $link)
                            <li><a class="button secondary" href="{{ $link['url'] }}">{{ $link['text'] }}</a></li>
                        @endforeach
                    </ul>
                @endforeach
            @endif
        </section>
    @endif
@endsection
