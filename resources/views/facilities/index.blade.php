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
        <ul class="facility-list">
            @foreach ($facilities as $facility)
                <li>
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
                    </article>
                </li>
            @endforeach
        </ul>

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
