@extends('layouts.app')

@section('content')
    <section class="card center">
        <img src="{{ asset('images/logo.png') }}" alt="{{ __('home.title') }} logo" style="width: 11rem; max-width: 100%; height: auto;">
        <h1>{{ __('home.title') }}</h1>
        <p class="tagline">{{ __('home.tagline') }}</p>
        <p>{{ __('home.mission') }}</p>
        <p><strong>{{ __('home.status') }}</strong></p>
        <div class="actions">
            <a class="button primary" href="https://github.com/AfyaLinkAfrica/afyalink-web">{{ __('home.contribute') }}</a>
            <a class="button secondary" href="https://github.com/AfyaLinkAfrica/afyalink-web/labels/good%20first%20issue">{{ __('home.issues') }}</a>
        </div>
    </section>
@endsection
