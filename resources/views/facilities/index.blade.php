@extends('layouts.app')

@section('title', $heading)

@push('styles')
<style>
    section.card { margin-bottom: 1.5rem; }
    h2 { margin: 0 0 .25rem; font-size: 1.2rem; line-height: 1.3; }
    .results-count { margin: 0 0 1rem; font-weight: 600; color: var(--text); }
    ul.facility-list { margin: 0; padding: 0; list-style: none; }
    ul.facility-list > li + li { margin-top: 1rem; }
    article.facility {
        padding: 1.25rem 1.5rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 1rem;
    }
    article.facility p { margin: .25rem 0; }
    nav.pagination { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1.5rem; }
    nav.pagination a, nav.pagination span.disabled {
        display: inline-block;
        min-width: 6rem;
        padding: .6rem 1rem;
        font-weight: 600;
        text-align: center;
        border: 2px solid var(--brand);
        border-radius: .5rem;
        text-decoration: none;
    }
    nav.pagination span.disabled { opacity: .45; border-color: var(--border); }
    [hidden] { display: none !important; }
    .near-me { margin: 0 0 1.5rem; }
    .near-me .actions { margin-top: 0; }
    button.button.outline { color: var(--brand-dark); background: transparent; }
    p.distance { font-weight: 600; color: var(--brand-dark); }
</style>
@endpush

@section('content')
    <h1>{{ $heading }}</h1>

    <section class="card" aria-label="{{ __('facilities.search_label') }}">
        @include('partials.facility-search', ['filters' => $filters])
    </section>

    <p class="results-count" role="status">
        {{ trans_choice('facilities.results_count', $facilities->total(), ['count' => $facilities->total()]) }}
    </p>

    @if ($facilities->contains(fn ($facility) => $facility->source === 'synthetic'))
        <p class="notice" role="note">{{ __('facilities.demo_notice') }}</p>
    @endif

    @if ($facilities->isEmpty())
        <p>{{ __('facilities.no_results') }}</p>
        @if ($hasFilters)
            <p><a href="{{ route('facilities.index') }}">{{ __('facilities.clear_filters') }}</a></p>
        @endif
    @else
        @php
            $hasLocations = $facilities->contains(fn ($facility) => $facility->latitude !== null && $facility->longitude !== null);
        @endphp

        @if ($hasLocations)
            {{-- Hidden until the script confirms the browser can share a location. --}}
            <section class="near-me" id="near-me" aria-label="{{ __('nearme.label') }}" hidden>
                <div class="actions">
                    <button type="button" class="button" id="near-me-sort">{{ __('nearme.sort') }}</button>
                    <button type="button" class="button outline" id="near-me-reset" hidden>{{ __('nearme.reset') }}</button>
                </div>
                <p class="help">{{ __('nearme.privacy') }} {{ __('nearme.page_only') }}</p>
                <p class="help" id="near-me-status" role="status"></p>
            </section>
        @endif

        <ul class="facility-list">
            @foreach ($facilities as $facility)
                <li @if ($facility->latitude !== null && $facility->longitude !== null) data-lat="{{ $facility->latitude }}" data-lng="{{ $facility->longitude }}" @endif>
                    <article class="facility">
                        <h2><a href="{{ route('facilities.show', $facility) }}">{{ $facility->name }}</a></h2>
                        @include('partials.facility-badges', ['facility' => $facility])
                        <p>{{ $facility->sub_county ? $facility->sub_county.', ' : '' }}{{ $facility->county }}</p>
                        @if ($facility->address)
                            <p>{{ $facility->address }}</p>
                        @endif
                        @if ($facility->phone)
                            <p>{{ __('facilities.call') }}: <a href="tel:{{ preg_replace('/[^\d+]/', '', $facility->phone) }}">{{ $facility->phone }}</a></p>
                        @endif
                        @if ($facility->services->isNotEmpty())
                            <ul class="services" aria-label="{{ __('facilities.services') }}">
                                @foreach ($facility->services as $service)
                                    <li>{{ $service->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                </li>
            @endforeach
        </ul>

        @if ($hasLocations)
            @php
                $nearMeText = [
                    'waiting' => __('nearme.waiting'),
                    'sorted' => __('nearme.sorted'),
                    'denied' => __('nearme.denied'),
                    'failed' => __('nearme.failed'),
                    'distance' => __('nearme.distance'),
                    'underOne' => __('nearme.under_one'),
                    'unknown' => __('nearme.unknown'),
                ];
            @endphp
            {{-- The location is read only after the visitor presses the button, used here, and never sent or stored. --}}
            <script>
                (function () {
                    var panel = document.getElementById('near-me');
                    var list = document.querySelector('ul.facility-list');

                    if (!panel || !list || !('geolocation' in navigator)) {
                        return;
                    }

                    var text = @json($nearMeText);
                    var sortButton = document.getElementById('near-me-sort');
                    var resetButton = document.getElementById('near-me-reset');
                    var status = document.getElementById('near-me-status');
                    var original = Array.prototype.slice.call(list.children);

                    panel.hidden = false;

                    function radians(degrees) {
                        return degrees * Math.PI / 180;
                    }

                    function distanceKm(fromLat, fromLng, toLat, toLng) {
                        var earthRadius = 6371;
                        var dLat = radians(toLat - fromLat);
                        var dLng = radians(toLng - fromLng);
                        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
                            + Math.cos(radians(fromLat)) * Math.cos(radians(toLat)) * Math.sin(dLng / 2) * Math.sin(dLng / 2);

                        return 2 * earthRadius * Math.asin(Math.sqrt(a));
                    }

                    function describe(km) {
                        if (km < 1) {
                            return text.underOne;
                        }

                        var shown = km < 10 ? km.toFixed(1) : String(Math.round(km));

                        return text.distance.replace(':distance', shown);
                    }

                    function clearDistances() {
                        Array.prototype.forEach.call(list.querySelectorAll('p.distance'), function (note) {
                            note.parentNode.removeChild(note);
                        });
                    }

                    function showDistances(position) {
                        var rows = original.map(function (item, index) {
                            var lat = parseFloat(item.getAttribute('data-lat'));
                            var lng = parseFloat(item.getAttribute('data-lng'));
                            var km = (isNaN(lat) || isNaN(lng))
                                ? null
                                : distanceKm(position.coords.latitude, position.coords.longitude, lat, lng);

                            return { item: item, index: index, km: km };
                        });

                        // Nearest first. Facilities without a recorded location go last, in their original order.
                        rows.sort(function (a, b) {
                            if (a.km === null && b.km === null) { return a.index - b.index; }
                            if (a.km === null) { return 1; }
                            if (b.km === null) { return -1; }

                            return (a.km - b.km) || (a.index - b.index);
                        });

                        clearDistances();

                        rows.forEach(function (row) {
                            var note = document.createElement('p');
                            note.className = 'distance';
                            note.textContent = row.km === null ? text.unknown : describe(row.km);
                            row.item.querySelector('article').appendChild(note);
                            list.appendChild(row.item);
                        });

                        status.textContent = text.sorted;
                        resetButton.hidden = false;
                        sortButton.disabled = false;
                    }

                    function failed(error) {
                        status.textContent = (error && error.code === 1) ? text.denied : text.failed;
                        sortButton.disabled = false;
                    }

                    sortButton.addEventListener('click', function () {
                        sortButton.disabled = true;
                        status.textContent = text.waiting;
                        navigator.geolocation.getCurrentPosition(showDistances, failed, {
                            enableHighAccuracy: false,
                            timeout: 10000,
                            maximumAge: 300000
                        });
                    });

                    resetButton.addEventListener('click', function () {
                        clearDistances();
                        original.forEach(function (item) { list.appendChild(item); });
                        resetButton.hidden = true;
                        status.textContent = '';
                    });
                })();
            </script>
        @endif

        @if ($facilities->hasPages())
            <nav class="pagination" aria-label="{{ __('facilities.pagination') }}">
                @if ($facilities->onFirstPage())
                    <span class="disabled" aria-disabled="true">{{ __('facilities.previous') }}</span>
                @else
                    <a href="{{ $facilities->previousPageUrl() }}" rel="prev">{{ __('facilities.previous') }}</a>
                @endif

                <span>{{ __('facilities.page_of', ['current' => $facilities->currentPage(), 'last' => $facilities->lastPage()]) }}</span>

                @if ($facilities->hasMorePages())
                    <a href="{{ $facilities->nextPageUrl() }}" rel="next">{{ __('facilities.next') }}</a>
                @else
                    <span class="disabled" aria-disabled="true">{{ __('facilities.next') }}</span>
                @endif
            </nav>
        @endif
    @endif
@endsection
