@if ($facility->latitude !== null && $facility->longitude !== null)
    @push('styles')
    <style>
        .map { margin-top: 1.5rem; }
        .map h2 { margin: 0 0 .75rem; font-size: 1.2rem; line-height: 1.3; }
        .map iframe { display: block; width: 100%; height: 20rem; border: 2px solid var(--border); border-radius: .5rem; }
    </style>
    @endpush

    <section class="card map" aria-labelledby="map-heading">
        <h2 id="map-heading">{{ __('facilities.map') }}</h2>

        @if ($mapUrl)
            <iframe
                src="{{ $mapUrl }}"
                title="{{ __('facilities.map_title') }}"
                loading="lazy"
                referrerpolicy="no-referrer"
                sandbox="allow-scripts allow-same-origin"
            ></iframe>
            <p class="help">{{ __('facilities.map_credit') }}</p>
            <p><a href="{{ route('facilities.show', $facility) }}">{{ __('facilities.hide_map') }}</a></p>
        @else
            <p class="help">{{ __('facilities.map_privacy') }}</p>
            <div class="actions">
                <a class="button secondary" href="{{ route('facilities.show', ['facility' => $facility, 'map' => 1]) }}">{{ __('facilities.show_map') }}</a>
            </div>
        @endif
    </section>
@endif
