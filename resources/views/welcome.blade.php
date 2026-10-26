@extends('layouts.app')

@push('styles')
<style>
    .hero { text-align: center; margin-bottom: 1.5rem; }
    .hero img { width: 5rem; max-width: 100%; height: auto; padding: .35rem; background: #fff; border-radius: .75rem; }
    .hero h1 { margin-top: .5rem; }
    section.card + section.card { margin-top: 1.5rem; }
    h2 { margin: 0 0 1rem; font-size: 1.35rem; line-height: 1.3; }
    ul.chip-links { display: flex; flex-wrap: wrap; gap: .5rem; margin: 0; padding: 0; list-style: none; }
    ul.chip-links a {
        display: inline-block;
        padding: .45rem .9rem;
        font-weight: 600;
        text-decoration: none;
        border: 2px solid var(--border);
        border-radius: 999px;
    }
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
        @include('partials.facility-search', ['filters' => [], 'services' => $services])
    </section>

    <section class="card" aria-labelledby="browse-heading">
        <h2 id="browse-heading">{{ __('home.browse_heading') }}</h2>
        <ul class="chip-links">
            @foreach (config('afyalink.featured_counties') as $county)
                <li><a href="{{ route('facilities.index', ['county' => $county]) }}">{{ $county }}</a></li>
            @endforeach
        </ul>
    </section>

    @if ($services->isNotEmpty())
        <section class="card" aria-labelledby="browse-services-heading">
            <h2 id="browse-services-heading">{{ __('home.browse_services_heading') }}</h2>
            <ul class="chip-links">
                @foreach ($services as $service)
                    <li><a href="{{ route('facilities.index', ['service' => $service->slug]) }}">{{ $service->name }}</a></li>
                @endforeach
            </ul>
        </section>
    @endif

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
