@extends('layouts.app')

@section('title', $facility->name)

@push('styles')
<style>
    .back { display: inline-block; margin-bottom: 1rem; font-weight: 600; }
    dl.details { display: grid; grid-template-columns: max-content 1fr; gap: .5rem 1.5rem; margin: 1rem 0 0; }
    dl.details dt { font-weight: 700; }
    dl.details dd { margin: 0; color: var(--muted); }
    @media (max-width: 30rem) {
        dl.details { grid-template-columns: 1fr; gap: 0; }
        dl.details dd { margin-bottom: .75rem; }
    }
</style>
@endpush

@section('content')
    <a class="back" href="{{ route('facilities.index') }}"><span aria-hidden="true">&larr;</span> {{ __('facilities.back') }}</a>

    @if ($facility->source === 'synthetic')
        <p class="notice" role="note">{{ __('facilities.demo_notice') }}</p>
    @endif

    <article class="card">
        <h1>{{ $facility->name }}</h1>

        @include('partials.facility-badges', ['facility' => $facility])

        @unless ($facility->isVerified())
            <p class="notice" role="note">{{ __('facilities.unverified_note') }}</p>
        @endunless

        <dl class="details">
            <dt>{{ __('facilities.county') }}</dt>
            <dd>{{ $facility->county }}</dd>

            @if ($facility->sub_county)
                <dt>{{ __('facilities.sub_county') }}</dt>
                <dd>{{ $facility->sub_county }}</dd>
            @endif

            @if ($facility->address)
                <dt>{{ __('facilities.address') }}</dt>
                <dd>{{ $facility->address }}</dd>
            @endif

            @if ($facility->phone)
                <dt>{{ __('facilities.phone') }}</dt>
                <dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $facility->phone) }}">{{ $facility->phone }}</a></dd>
            @endif

            @if ($facility->services->isNotEmpty())
                <dt>{{ __('facilities.services') }}</dt>
                <dd>
                    <ul class="services">
                        @foreach ($facility->services as $service)
                            <li>{{ $service->name }}</li>
                        @endforeach
                    </ul>
                </dd>
            @endif

            <dt>{{ __('facilities.last_verified') }}</dt>
            <dd>{{ $facility->isVerified() ? $facility->verified_at->translatedFormat('j F Y') : __('facilities.not_verified') }}</dd>

            @if ($facility->latitude !== null && $facility->longitude !== null)
                <dt>{{ __('facilities.location') }}</dt>
                <dd><a href="https://www.openstreetmap.org/?mlat={{ $facility->latitude }}&amp;mlon={{ $facility->longitude }}#map=16/{{ $facility->latitude }}/{{ $facility->longitude }}">{{ __('facilities.open_map') }}</a></dd>
            @endif
        </dl>
    </article>
@endsection
