@extends('layouts.app')

@push('styles')
<style>
    .hero { text-align: center; margin-bottom: 1.5rem; }
    .hero img { width: 5rem; max-width: 100%; height: auto; padding: .35rem; background: #fff; border-radius: .75rem; }
    .hero h1 { margin-top: .5rem; }
    section.card + section.card { margin-top: 1.5rem; }
    h2 { margin: 0 0 1rem; font-size: 1.35rem; line-height: 1.3; }
    .field { margin-bottom: 1rem; }
    label { display: block; margin-bottom: .25rem; font-weight: 600; }
    select, input[type="text"] {
        width: 100%;
        min-height: 2.75rem;
        padding: .5rem .75rem;
        font: inherit;
        color: var(--text);
        background: var(--bg);
        border: 2px solid var(--border);
        border-radius: .5rem;
    }
    select:focus-visible, input[type="text"]:focus-visible, button:focus-visible {
        outline: 3px solid var(--brand-dark);
        outline-offset: 2px;
    }
    button.button {
        min-height: 2.75rem;
        padding: .7rem 1.2rem;
        font: inherit;
        font-weight: 600;
        cursor: pointer;
        color: #fff;
        background: var(--brand);
        border: 2px solid var(--brand);
        border-radius: .5rem;
    }
    ul.county-links { display: flex; flex-wrap: wrap; gap: .5rem; margin: 0; padding: 0; list-style: none; }
    ul.county-links a {
        display: inline-block;
        padding: .45rem .9rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid var(--border);
        border-radius: 999px;
    }
    ol.steps { margin: 0; padding-left: 1.5rem; }
    ol.steps li { margin-bottom: .5rem; color: var(--muted); }
    ol.steps li:last-child { margin-bottom: 0; }
</style>
@endpush

@section('content')
    <section class="hero">
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('home.title') }} logo">
        <h1>{{ __('home.title') }}</h1>
        <p class="tagline">{{ __('home.tagline') }}</p>
    </section>

    <section class="card" aria-labelledby="search-heading">
        <h2 id="search-heading">{{ __('home.search_heading') }}</h2>
        <form method="get" action="{{ route('facilities.index') }}" role="search">
            <div class="field">
                <label for="county">{{ __('facilities.county') }}</label>
                <select id="county" name="county">
                    <option value="">{{ __('facilities.any_county') }}</option>
                    @foreach (config('afyalink.counties') as $county)
                        <option value="{{ $county }}">{{ $county }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="type">{{ __('facilities.type') }}</label>
                <select id="type" name="type">
                    <option value="">{{ __('facilities.any_type') }}</option>
                    @foreach (\App\Models\Facility::TYPES as $type)
                        <option value="{{ $type }}">{{ __('facilities.types.'.$type) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="q">{{ __('facilities.name') }}</label>
                <input id="q" name="q" type="text" maxlength="100" placeholder="{{ __('facilities.name_placeholder') }}">
            </div>

            <button type="submit" class="button">{{ __('facilities.search_button') }}</button>
        </form>
    </section>

    <section class="card" aria-labelledby="browse-heading">
        <h2 id="browse-heading">{{ __('home.browse_heading') }}</h2>
        <ul class="county-links">
            @foreach (config('afyalink.featured_counties') as $county)
                <li><a href="{{ route('facilities.index', ['county' => $county]) }}">{{ $county }}</a></li>
            @endforeach
        </ul>
    </section>

    <section class="card" aria-labelledby="how-heading">
        <h2 id="how-heading">{{ __('home.how_heading') }}</h2>
        <ol class="steps">
            <li>{{ __('home.how_step_search') }}</li>
            <li>{{ __('home.how_step_check') }}</li>
            <li>{{ __('home.how_step_contact') }}</li>
        </ol>
    </section>

    <section class="card" aria-labelledby="about-heading">
        <h2 id="about-heading">{{ __('home.about_heading') }}</h2>
        <p>{{ __('home.mission') }}</p>
        <p><strong>{{ __('home.status') }}</strong></p>
        <div class="actions">
            <a class="button primary" href="https://github.com/AfyaLinkAfrica/afyalink-web">{{ __('home.contribute') }}</a>
            <a class="button secondary" href="https://github.com/AfyaLinkAfrica/afyalink-web/labels/good%20first%20issue">{{ __('home.issues') }}</a>
        </div>
    </section>
@endsection
